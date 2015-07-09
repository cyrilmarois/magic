<?php

namespace app\commands;

use app\components\Utilities;
use app\components\SimpleDom;
use app\models\Card;
use app\models\Set;
use yii\helpers\Html;
use yii\console\Controller;
use Exception;
use Yii;

class MagicImporterV1Controller  extends Controller
{
    const GATHERED_WIZARD_URL = 'http://gatherer.wizards.com/';

    private $_verbose;

    /**
     * populate sets
     *
     * @param null|string $setName
     * @param bool        $forceImport
     * @param bool        $verbose
     * @throws Exception
     *
     * @return void
     */
    public function actionIndex($setName = null, $forceImport = false, $verbose = true)
    {
        try {
            Yii::trace('Trace :'.__METHOD__, __METHOD__);

            $sets = Set::getSets();
            $forceImport = (bool)$forceImport;
            $this->_verbose = $verbose;
            $html = new SimpleDom();
            $html->load_file('http://magic.wizards.com/en/game-info/products/card-set-archive');
            foreach($html->find('#content .page-width ul li a') as $i => $data){
                $keepProcessing = true;
                $htmlSetName = Utilities::cleanQuotes($data->find('.nameSet', 0)->plaintext);

                //we check that the sets belongs to classic magic sets
                if (isset($sets[$htmlSetName]) === true) {
                    if (($forceImport === false) && (($this->getSet($htmlSetName) !== null) || (($setName === $htmlSetName) && ($this->getSet($setName) !== null)))) {
                        echo 'Import du set : ' . $htmlSetName . ' annulé car il existe déjà en BDD' . "\n";
                        $keepProcessing = false;
                    } elseif ($forceImport === true) {
                        if (($setName === $htmlSetName) && ($this->getSet($setName) !== null)) {
                            $set = $this->getSet($setName);
                            $set->scenario = 'update';
                        } elseif ($this->getSet($htmlSetName) !== null) {
                            $set = $this->getSet($htmlSetName);
                            $set->scenario = 'update';
                        } else {
                            $set = new Set(['scenario' => 'create']);
                        }
                    }

                    if ($keepProcessing === true) {
                        $set->setName = $htmlSetName;

                        $set->setLogoUrl = $data->find('.logo img', 0)->src;
                        $set->setIconUrl = $data->find('.icon img', 0)->src;
                        $set->setYear = self::parseDate($data->find('.date-display-single', 0)->plaintext);

                        $status = (($set->validate() === true) && ($set->save() === true));

                        if ($status === true) {
                            if ($this->_verbose === true) {
                                echo 'Import du set : ' . $set->setName . "\n";
                            }

                            $setUrl = $sets[$set->setName];
                            $setUrl = str_replace(' ', '%20', $setUrl);
                            $newHtml = new SimpleDom();
                            $newHtml->load_file(self::GATHERED_WIZARD_URL . '/Pages/Search/Default.aspx?action=advanced&set=[%22' . $setUrl . '%22]');
                            $pages = self::getPages($newHtml->find('.pagingcontrols .paging > a'));

                            if (count($pages) > 0) {
                                $this->populateCard($set, $pages);
                            } else {
                                $this->populateCard($set, ['/Pages/Search/Default.aspx?action=advanced&set=[%22' . $setUrl . '%22]']);
                            }
                        }

                    }
                }
            }


            echo 'Like a BOSS !! '. Card::find()->count() .' cartes ont été importées'."\n";
            echo "Bilan :\n";
            $sets = Set::find()->all();
            foreach($sets as $set) {
                $countCard = Card::find()->where(['setId' => $set->setId])->count();
                if ($this->_verbose === true) {
                    echo $set->setName . ', import de : ' .$countCard. " cartes.\n";
                }
                $this->log($set->setName . ', import de : ' .$countCard. ' cartes');
            }
        } catch(Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            throw $e;
        }
    }

    /**
     * populate cards of the current set
     *
     * @param Set  $set
     * @param Array $pages
     * @throws Exception
     *
     * @return void
     */
    public function populateCard($set, $pages)
    {
        try {
            Yii::trace('Trace :'.__METHOD__, __METHOD__);

            $countCard = 1;
            foreach($pages as $i => $page) {
                $url = html_entity_decode(self::GATHERED_WIZARD_URL . $page);

                $html = new SimpleDom();
                $html->load_file($url);

                $totalCard = $this->parseTotalCard($html->find('.termdisplay span', 0)->plaintext);
                if ($this->_verbose === true) {
                    echo $totalCard . " cartes sur la page \n";
                }

                $status = self::hasCards($html->find('.cardItemTable > table'));
                if ($status === true) {
                    //fetch and analyzes all pages
                    foreach ($html->find('table.cardItemTable > table') as $table) {
                        //card name
                        $htmlCardName = $table->find('.cardTitle a', 0)->plaintext;

                        $card = $this->getCard($htmlCardName, $set->setId);
                        if ($card === null) {
                            $card = new Card(['scenario' => 'create']);
                        } else {
                            $card->scenario = 'update';
                        }
                        //loading default values;
                        $card->loadDefaultValues();

                        $card->cardNameVO = $htmlCardName;

                        //mana card cost
                        $cardCost = null;
                        foreach ($table->find('.manaCost img') as $cost) {
                            $cardCost .= $this->parseCost($cost->src);
                        }
                        $card->cardCost = ($cardCost !== null) ? $cardCost : 0;

                        //card type
                        $card->cardType = $this->parseType($table->find('.typeLine', 0)->plaintext);

                        //card monster energy
                        $cardTypes = Card::getCardTypes();
                        if (in_array($card->cardType, $cardTypes['creatures']) === true) {
                            //we're looking for the attack and endurance of the creature
                            $card->cardMonsterEnergy = $this->parseEnergy($table->find('.typeLine', 0)->plaintext);
                        }

                        $card->cardDescriptionVO = Utilities::cleanStr($table->find('.rulesText', 0)->innertext);

                        //card picture url
                        $card->cardPictureVO = $this->savePicture($table->find('.leftCol a img', 0)->src, $card->cardNameVO, $set->setName);

                        //card icon url
                        $card->cardRarity = $this->saveRarityIcon($table->find('.rightCol  a img', 0), $card->cardNameVO, $set->setName);
                        //set
                        $card->setId = $set->setId;

                        $status = $card->validate();

                        if ($status === true) {
                            $status = $card->save();
                            if (($status === true) && ($this->_verbose === true)) {
                                echo 'Import de la carte ' .$countCard. ' sur ' . $totalCard. "\n";
                            } else {
                                $this->log('Error saving card : ' . $card->cardNameVO . ' in set : ' . $set->setName);
                            }
                            $countCard++;
                        }
                    }
                } else {
                    echo 'Aucune carte pour le set : ' . $set->setName . "\n";
                    $this->log($set->setName . ' has no cards');
                }
            }
            $this->log('Import de ' .$countCard. ' cartes pour le set : ' .$set->setName);
        } catch(Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            throw $e;
        }
    }

    /**
     * get the value of the name into the string
     *
     * @param string $str the string to parsed
     *
     * @return string $cost the mana cost
     */
    public function parseCost($str)
    {
        $cost = null;
        $pattern = '/name=(\d|[A-Z]){1}/';            //find something like name=5 or name=A
        $subject = $str;
        preg_match($pattern, $subject, $matches);
        if ((count($matches) > 0) && (isset($matches[1]) === true)) {
            //we are looking for the cost
            //it must be the second element of the array
            $cost = Utilities::cleanStr($matches[1]);
        }
        return $cost;
    }

    /**
     * get the type
     *
     * @param $str
     *
     * @return null|string
     */
    public function parseType($str)
    {
        $type = null;
        $explode = explode('—', $str);
        if ((count($explode) > 0) && (isset($explode[0]) === true)){
            //we are looking for the type
            //it must be the first element of the array
            $type = Utilities::cleanStr($explode[0]);
        }
        return $type;
    }

    /**
     * get the type
     *
     * @param $str
     *
     * @return null|string
     */
    public function parseEnergy($str)
    {
        $energy = null;
        $explode = explode('—', $str);
        if ((count($explode) > 0) && (isset($explode[1]) === true)){
            $data = trim($explode[1]);
            $energy = Utilities::cleanStr(substr($data, -4, 3));
        }
        return $energy;
    }

    /**
     * save picture
     *
     * @param $setName
     * @param $cardName
     * @param $cardName
     *
     * @return bool
     */
    public function savePicture($url, $cardName, $setName)
    {
        $picture = Card::DEFAULT_PICTURE_CARD;
        $explode = explode('../../', $url);
        if ((count($explode) > 0 === true) && (isset($explode[1]) === true)) {
             $setName = $this->specialClean($setName);
             $uploadPath = '/var/www/html/sites/magic/images/cards/'.$setName;
             if (file_exists($uploadPath) === false) {
                 mkdir($uploadPath, 0777, true);
             } elseif((is_dir($uploadPath) === true) && (is_writable($uploadPath) === false)) {
                 chmod($uploadPath, 0777);
             }
             $picture = $this->specialClean($cardName);
             $picture .= '.png';
             if (file_exists($uploadPath.DIRECTORY_SEPARATOR.$picture) === false) {
                 $pictureUrl = Utilities::cleanStr(Utilities::GATHERED_WIZARD_URL.$explode[1]);
                 $status = file_put_contents($uploadPath . DIRECTORY_SEPARATOR . $picture, file_get_contents($pictureUrl));
                 if ($this->_verbose === true) {
                     if ($status !== false) {
                         echo 'Enregistrement de l\'image de la carte ' .$cardName. ' avec succès'. "\n";
                     } else {
                         echo 'Erreur lors de l\enregistrement de l\'image de la carte ' .$cardName. "\n";
                     }
                 }
             }

        }
        return $picture;
    }

    /**
     * save icon
     *
     * @param $url
     *
     * @return null|string
     */
    public function saveRarityIcon($icon, $cardName, $setName)
    {
        $explode = explode('../../', $icon->src);
        if ((count($explode) > 0 === true) && (isset($explode[1]) === true)) {
            $setName = $this->specialClean($setName);
            $uploadPath = '/var/www/html/sites/magic/images/icons/'.$setName;
            if (file_exists($uploadPath) === false) {
                mkdir($uploadPath, 0777, true);
            } elseif((is_dir($uploadPath) === true) && (is_writable($uploadPath) === false)) {
                chmod($uploadPath, 0777);
            }

            $iconUrl = $explode[1];
            $iconUrlLength = strlen($iconUrl);
            $icon = (isset($iconUrl[$iconUrlLength - 1]) === true) ? $iconUrl[$iconUrlLength - 1]: null;
            if (file_exists($uploadPath.DIRECTORY_SEPARATOR.$icon.'.png') === false) {
                $status = file_put_contents($uploadPath . DIRECTORY_SEPARATOR . $icon.'.png', file_get_contents(Html::decode(self::GATHERED_WIZARD_URL.$iconUrl)));
                if ($this->_verbose === true) {
                    if ($status !== false) {
                        echo 'Enregistrement de l\'icon de la carte ' . $cardName . ' avec succès' . "\n";
                    } else {
                        echo 'Erreur lors de l\enregistrement de l\'image de la carte ' . $cardName . "\n";
                    }
                }
            }
        }
        return $icon;
    }

    private $_logFile = 'magic_logs.txt';

    /**
     * log messages
     *
     * @param $message
     *
     * @return void
     */
    public function log($message)
    {
        $file = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.$this->_logFile;
        $data = '['.date('Y-m-d H:i:s').'] : '.$message."\n";
        file_put_contents($file, $data, FILE_APPEND);
    }

    /**
     * get set if exists
     *
     * @param $setName
     *
     * @return null|Object
     */
    public function getSet($setName)
    {
        return Set::find()->where(['setName' => $setName])->one();
    }

    /**
     * get card if exists
     *
     * @param string $cardName
     * @param string $setId
     *
     * @return null|Object
     */
    public function getCard($cardName, $setId)
    {
        return Card::find()->where(['cardNameVO' => Utilities::cleanStr($cardName), 'setId' => $setId])->one();
    }

    public function parseTotalCard($str)
    {
        //set default date to this year
        $total = 0;
        preg_match('/\((\d+)\)/', $str, $matches);
        if (isset($matches[1]) === true) {
            $total = Utilities::cleanStr($matches[0]);
        }
        return $total;
    }




    /**
     * check if the $set page has card
     *
     * @param $str
     *
     * @return bool
     */
    public static function hasCards($str)
    {
        return count($str) > 0;
    }

    /**
     * get the date of the set
     *
     * @param $str
     *
     * @return string the date of the set or current year by default
     */
    public static function parseDate($str)
    {
        //set default date to this year
        $date = date('Y');
        preg_match('/(\d+)/', $str, $matches);
        if (isset($matches[0]) === true) {
            $date = Utilities::cleanStr($matches[0]);
        }
        return $date;
    }

    /**
     * get all pages link
     *
     * @param $pages
     *
     * @return Array $pagesLink
     */
    public static function getPages(Array $pages)
    {
        $pagesLink = [];
        foreach($pages as $page) {
            if (in_array($page->href, $pagesLink) === false) {
                $pagesLink[] = $page->href;
            }
        }
        return $pagesLink;
    }

    /**
     * remove special chars and spaces
     *
     * @param $cardName
     *
     * @return clean cardName
     */
    public function specialClean($cardName)
    {
        $a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ');
        $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
        $str = str_replace($a, $b, $cardName);
        return preg_replace(['/(\s|-|_|\'|\/)+/'], ['-'], $str);
    }
}
