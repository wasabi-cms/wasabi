<?php
/**
 *
 * PHP 5
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the below copyright notice.
 *
 * @copyright     Copyright 2013, Frank Förster (http://frankfoerster.com)
 * @link          http://github.com/frankfoerster/wasabi
 * @package       Wasabi
 * @subpackage    Wasabi.Plugin.Core.Model.Behavior
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('CakeException', 'Error');
App::uses('Hash', 'Utility');
App::uses('ModelBehavior', 'Model');

class SluggableBehavior extends ModelBehavior {

	/**
	 * Holds the different character mappings.
	 *
	 * @var array
	 */
	protected $_charMappings = array(
		'Ā' => 'A', 'Ă' => 'A', 'Ą' => 'A', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Å' => 'A', 'Ǎ' => 'A', 'Ǟ' => 'A', 'Ǡ' => 'A', 'Ǻ' => 'A', 'Ȁ' => 'A', 'Ȃ' => 'A', 'Ȧ' => 'A', 'Ⱥ' => 'A', 'Ǽ' => 'AE', 'Ǣ' => 'AE', 'Æ' => 'AE', 'Ä' => 'Ae',
		'ā' => 'a', 'ă' => 'a', 'ą' => 'a', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'å' => 'a', 'ǎ' => 'a', 'ǟ' => 'a', 'ǡ' => 'a', 'ǻ' => 'a',	'ȁ' => 'a', 'ȃ' => 'a', 'ȧ' => 'a', 'ǽ' => 'ae', 'ǣ' => 'ae', 'ä' => 'ae', 'æ' => 'ae',
		'Ɓ' => 'B', 'Ƃ' => 'B', 'Ƅ' => 'B', 'Ƀ' => 'B',
		'ƀ' => 'b', 'ƃ' => 'b', 'ƅ' => 'b',
		'Ć' => 'C', 'Ĉ' => 'C', 'Ċ' => 'C', 'Č' => 'C', 'Ç' => 'C', 'Ƈ' => 'C', 'Ȼ' => 'C',
		'ȼ' => 'c', 'ć' => 'c', 'ĉ' => 'c', 'ċ' => 'c', 'č' => 'c', 'ç' => 'c', 'ƈ' => 'c',
		'Ď' => 'D', 'Đ' => 'D', 'Ð' => 'D', 'Ɖ' => 'D', 'Ɗ' => 'D', 'Ƌ' => 'D', 'Ǆ' => 'DZ', 'Ǳ' => 'DZ', 'ǅ' => 'Dz',
		'ď' => 'd', 'đ' => 'd', 'ƌ' => 'd', 'ƍ' => 'd', 'ȡ' => 'd', 'ȸ' => 'db', 'ǆ' => 'dz', 'ǲ' => 'dz', 'ǳ' => 'dz',
		'Ē' => 'E', 'Ĕ' => 'E', 'Ė' => 'E', 'Ę' => 'E', 'Ě' => 'E', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ǝ' => 'E', 'Ə' => 'E', 'Ɛ' => 'E', 'Ʃ' => 'E', 'Ʒ' => 'E', 'Ƹ' => 'E', 'Ǯ' => 'E', 'Ȅ' => 'E',	'Ȇ' => 'E', 'Ȩ' => 'E', 'Ɇ' => 'E',
		'ɇ' => 'e', 'ȩ' => 'e', 'ǯ' => 'e', 'ȅ' => 'e', 'ȇ' => 'e', 'ē' => 'e', 'ĕ' => 'e', 'ė' => 'e', 'ę' => 'e', 'ě' => 'e', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ƹ' => 'e', 'ƺ' => 'e', 'ǝ' => 'e',
		'Ƒ' => 'F',
		'ƒ' => 'f',
		'Ĝ' => 'G', 'Ğ' => 'G', 'Ġ' => 'G', 'Ģ' => 'G', 'Ɠ' => 'G', 'Ɣ' => 'G', 'Ǥ' => 'G', 'Ǧ' => 'G', 'Ǵ' => 'G',
		'ǥ' => 'g', 'ǧ' => 'g', 'ǵ' => 'g', 'ĝ' => 'g', 'ğ' => 'g', 'ġ' => 'g', 'ģ' => 'g',
		'Ĥ' => 'H', 'Ħ' => 'H', 'Ȟ' => 'H', 'ƕ' => 'HV', 'Ƕ' => 'Hw',
		'ȟ' => 'h', 'ĥ' => 'h', 'ħ' => 'h',
		'Ĩ' => 'I', 'Ī' => 'I', 'Ĭ' => 'I', 'Į' => 'I', 'İ' => 'I', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ɩ' => 'I',	'Ɨ' => 'I', 'Ǐ' => 'I', 'Ȉ' => 'I', 'Ȋ' => 'I', 'Ĳ' => 'IJ',
		'ȉ' => 'i', 'ȋ' => 'i', 'ǐ' => 'i', 'ĩ' => 'i', 'ī' => 'i', 'ĭ' => 'i', 'į' => 'i', 'ı' => 'i', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ĳ' => 'ij',
		'Ĵ' => 'J', 'Ɉ' => 'J',
		'ǰ' => 'j', 'ȷ' => 'j', 'ɉ' => 'j', 'ĵ' => 'j',
		'Ķ' => 'K', 'ĸ' => 'K', 'Ǩ' => 'K', 'Ƙ' => 'K',
		'ƙ' => 'k', 'ǩ' => 'k', 'ķ' => 'k',
		'Ĺ' => 'L', 'Ļ' => 'L', 'Ľ' => 'L', 'Ŀ' => 'L', 'Ł' => 'L', 'Ƚ' => 'L', 'Ǉ' => 'LJ', 'ǈ' => 'Lj',
		'ȴ' => 'l', 'ƚ' => 'l', 'ƛ' => 'l', 'ƪ' => 'l', 'ĺ' => 'l', 'ļ' => 'l', 'ľ' => 'l', 'ŀ' => 'l', 'ł' => 'l', 'ǉ' => 'lj',
		'Ɯ' => 'M',
		'Ń' => 'N', 'Ņ' => 'N', 'Ň' => 'N', 'Ŋ' => 'N', 'Ñ' => 'N', 'Ɲ' => 'N', 'Ǹ' => 'N', 'Ƞ' => 'N', 'Ǌ' => 'NJ', 'ǋ' => 'Nj',
		'ǹ' => 'n', 'ȵ' => 'n', 'ƞ' => 'n', 'ń' => 'n', 'ņ' => 'n', 'ň' => 'n', 'ŉ' => 'n', 'ŋ' => 'n', 'ñ' => 'n', 'ǌ' => 'nj',
		'Ō' => 'O', 'Ŏ' => 'O', 'Ő' => 'O', 'Ɔ' => 'O', 'Ɵ' => 'O', 'Ơ' => 'O', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ɔ' => 'O', 'Ɵ' => 'O', 'Ơ' => 'O', 'Ø' => 'O', 'Ǒ' => 'O', 'Ǿ' => 'O', 'Ȍ' => 'O', 'Ȏ' => 'O', 'Ȫ' => 'O', 'Ȭ' => 'O', 'Ȯ' => 'O', 'Ȱ' => 'O', 'Ȣ' => 'OU', 'Ƣ' => 'OI', 'Ö' => 'Oe',
		'ǒ' => 'o', 'ǿ' => 'o', 'ȍ' => 'o', 'ȏ' => 'o', 'ȫ' => 'o', 'ȭ' => 'o', 'ȯ' => 'o', 'ȱ' => 'o', 'ō' => 'o', 'ŏ' => 'o', 'ő' => 'o', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ø' => 'o', 'ơ' => 'o', 'ƣ' => 'oi', 'ö' => 'oe', 'œ' => 'oe', 'ȣ' => 'ou',
		'Ƥ' => 'P',
		'ƥ' => 'p', 'ƿ' => 'p',
		'Ǫ' => 'Q', 'Ǭ' => 'Q', 'Ɋ' => 'Q',
		'ɋ' => 'q', 'ǫ' => 'q', 'ǭ' => 'q', 'ȹ' => 'qp',
		'Ŕ' => 'R', 'Ŗ' => 'R', 'Ř' => 'R', 'Ȑ' => 'R', 'Ȓ' => 'R', 'Ɍ' => 'R',
		'ɍ' => 'r', 'ȑ' => 'r', 'ȓ' => 'r', 'ŕ' => 'r', 'ŗ' => 'r', 'ř' => 'r', 'ſ' => 'r',
		'Ś' => 'S', 'Ŝ' => 'S', 'Ş' => 'S', 'Š' => 'S', 'Ș' => 'S',
		'ș' => 's', 'ȿ' => 's', 'ś' => 's', 'ŝ' => 's', 'ş' => 's', 'š' => 's', 'ß' => 'ss',
		'Ţ' => 'T', 'Ť' => 'T', 'Ŧ' => 'T', 'Ț' => 'T', 'Ƨ' => 'T', 'Ƭ' => 'T', 'Ʈ' => 'T', 'Ⱦ' => 'T',
		'ƨ' => 't', 'ƫ' => 't', 'ƭ' => 't', 'ț' => 't', 'ȶ' => 't', 'ţ' => 't', 'ť' => 't', 'ŧ' => 't',
		'Ũ' => 'U', 'Ū' => 'U', 'Ŭ' => 'U', 'Ů' => 'U', 'Ű' => 'U', 'Ų' => 'U', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ư' => 'U', 'Ʊ' => 'U', 'Ʋ' => 'U', 'Ǔ' => 'U', 'Ǖ' => 'U', 'Ǘ' => 'U', 'Ǚ' => 'U', 'Ǜ' => 'U', 'Ʉ' => 'U', 'Ȕ' => 'U', 'Ȗ' => 'U', 'Ü' => 'Ue',
		'ȕ' => 'u', 'ȗ' => 'u', 'ư' => 'u', 'ǔ' => 'u', 'ǖ' => 'u', 'ǘ' => 'u', 'ǚ' => 'u', 'ǜ' => 'u', 'ũ' => 'u', 'ū' => 'u', 'ŭ' => 'u', 'ů' => 'u', 'ű' => 'u', 'ų' => 'u', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'ue',
		'Ʌ' => 'V',
		'Ŵ' => 'W', 'Ƿ' => 'W',
		'ŵ' => 'w',
		'Ŷ' => 'Y', 'Ÿ' => 'Y', 'Ý' => 'Y', 'Ƴ' => 'Y', 'Ȝ' => 'Y', 'Ȳ' => 'Y', 'Ʀ' => 'YR',
		'ȝ' => 'y', 'ȳ' => 'y', 'ŷ' => 'y', 'ý' => 'y', 'ÿ' => 'y', 'ƴ' => 'y', 'Ɏ' => 'Y', 'ɏ' => 'y',
		'Ź' => 'Z', 'Ż' => 'Z', 'Ž' => 'Z', 'Ƶ' => 'Z', 'Ȥ' => 'Z',
		'ź' => 'z', 'ż' => 'z', 'ž' => 'z', 'ƶ' => 'z', 'ȥ' => 'z', 'ɀ' => 'z',

		'А' => 'A', 'Б' => 'B', 'В' => 'W', 'Г' => 'G', 'Д' => 'D', 'Е' => 'Ie', 'Ё' => 'Io', 'Ж' => 'Z',
		'З' => 'Z', 'И' => 'I', 'Й' => 'J', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O',
		'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'Ch', 'Ц' => 'C',
		'Ч' => 'Tch', 'Ш' => 'Sh', 'Щ' => 'Shtch', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'Iu',
		'Я' => 'Ia', 'а' => 'a', 'б' => 'b', 'в' => 'w', 'г' => 'g', 'д' => 'd', 'е' => 'ie', 'ё' => 'io',
		'ж' => 'z', 'з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n',
		'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'ch',
		'ц' => 'c', 'ч' => 'tch', 'ш' => 'sh', 'щ' => 'shtch', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e',
		'ю' => 'iu', 'я' => 'ia',

		'&' => 'and', '@' => 'at',
		'¢' => 'Cent', '£' => 'Pound', '¥' => 'Yen',
		'Þ' => 'T', 'ð' => 'e', 'þ' => 't',
		'ƻ' => '2', 'ƾ' => '3', 'Ƽ' => '5', 'ƽ' => '5',
	);

	/**
	 * Holds the behavior settings for each model.
	 *
	 * @var array
	 */
	protected $_settings = array();

	/**
	 * Setup the behavior with supplied settings
	 *
	 * field: (string) the database field which is used to generate the slug
	 * separator: (string) the separator used to combine words in in the slug
	 * slugField: (string) the database field where the slug is saved
	 * delimiters: (array) a list of additional delimiters that should be replaced by a separator
	 * lowercase: (boolean) true -> if the generated slug should be lowercase
	 * checkTree: (boolean) true -> if the uniqueness of a slug should be checked its current tree path level
	 * charMappings: (array) key => value, custom charMappings that are used as character replacements. overrides the defaults
	 *
	 * @param Model $model
	 * @param array $settings
	 */
	public function setup(Model $model, $settings = array()) {
		$defaults = array(
			'field' => 'name',
			'separator' => '-',
			'slugField' => 'slug',
			'delimiters' => array(),
			'lowercase' => true,
			'checkTree' => true,
			'charMappings' => $this->_charMappings
		);
		$this->_settings[$model->alias] = Hash::merge($defaults, $settings);
	}

	/**
	 * beforeSave callback
	 * runs before model data is saved
	 *
	 * @param Model $model Model using the behavior
	 * @return boolean
	 * @throws CakeException
	 */
	public function beforeSave(Model $model) {
		$settings = $this->_settings[$model->alias];
		$field = $settings['field'];
		$slugField = $settings['slugField'];

		if (!$model->hasField($field)) {
			throw new CakeException('The field `' . $field . '` is missing from DB table `' . $model->table . '`.');
		}

		if (!$model->hasField($slugField)) {
			throw new CakeException('The field `' . $slugField . '` is missing from DB table `' . $model->table . '`.');
		}

		$fieldContent = '';
		if (isset($model->data[$model->alias][$field]) && !empty($model->data[$model->alias][$field])) {
			$fieldContent = $model->data[$model->alias][$field];
		}

		if ($fieldContent == '') {
			return parent::beforeSave($model);
		}

		$slug = $this->generateSlug($model, $fieldContent);
		$slug = $this->makeSlugUnique($model, $slug);

		$model->data[$model->alias][$slugField] = $slug;

		return parent::beforeSave($model);
	}

	/**
	 * Generate a slug from $content
	 *
	 * @param Model $model
	 * @param string $content
	 * @return string
	 */
	public function generateSlug(Model $model, $content) {
		$slug = strtr($content, $this->_settings[$model->alias]['charMappings']);
		if ($this->_settings[$model->alias]['lowercase'] === true) {
			$slug = strtolower($slug);
		}
		if (count($this->_settings[$model->alias]['delimiters'])) {
			$slug = preg_replace("/[" . implode('', $this->_settings[$model->alias]['delimiters']) . "]/", $this->_settings[$model->alias]['separator'], $slug);
		}
		$slug = preg_replace("/[^a-zA-Z0-9\s-]/", '', $slug);
		$slug = trim(preg_replace("/[\s-]+/", ' ', $slug));
		$slug = preg_replace("/\s/", $this->_settings[$model->alias]['separator'], $slug);
		return $slug;
	}

	/**
	 * Check the uniqueness of a $slug and append an index if neccessary.
	 * If checkTree setting is set to true and the Tree behavior is loaded on the model,
	 * the uniqueness of the slug will be checked concerning its tree path.
	 *
	 * This allows for unique checks on separate path levels like the example below.
	 *
	 * about
	 * about-1
	 * company/about
	 * company/about-1
	 *
	 * @param Model|TreeBehavior $model
	 * @param string $slug
	 * @return string
	 */
	public function makeSlugUnique(Model $model, $slug) {
		$options = array();
		if (isset($model->data[$model->alias]['id']) && !empty($model->data[$model->alias]['id'])) {
			$options['conditions'] = array($model->alias . '.id <>' => $model->data[$model->alias]['id']);
		}
		$modelEntries = $model->find('all', $options);

		if ($this->_settings[$model->alias]['checkTree'] === true && $model->Behaviors->loaded('Tree')) {
			$paths = array();
			foreach ($modelEntries as $entry) {
				$paths[] = implode('|', Set::extract('/' . $model->alias . '/' . $this->_settings[$model->alias]['slugField'], $model->getPath($entry[$model->alias]['id'])));
			}
			$currentPath = array();
			if (!isset($model->data[$model->alias]['id'])) {
				if (!isset($model->data[$model->alias]['parent_id'])) {
					$currentPath[] = $model->data[$model->alias];
				} else {
					$currentPath = $model->getPath($model->data[$model->alias]['parent_id']);
					$currentPath[] = $model->data[$model->alias];
				}
			} else {
				$currentPath = $model->getPath($model->data[$model->alias]['id']);
			}
			if (array_pop($currentPath) !== null) {
				$currentPath = implode('|', Set::extract('/' . $model->alias . '/' . $this->_settings[$model->alias]['slugField'], $currentPath));
			}
			if ($currentPath != '') {
				$currentPath .= '|';
			}
			$beginningSlug = $slug;
			$path = $currentPath . $beginningSlug;
			$i = 1;
			$currentSlug = $beginningSlug;
			while (in_array($path, $paths)) {
				$currentSlug = $beginningSlug . $this->_settings[$model->alias]['separator'] . $i++;
				$path = $currentPath . $currentSlug;
			}
		} else {
			$existingSlugs = Set::extract('/' . $model->alias . '/' . $this->_settings[$model->alias]['slugField'], $modelEntries);
			$i = 1;
			$beginningSlug = $slug;
			$currentSlug = $slug;
			while (in_array($currentSlug, $existingSlugs)) {
				$currentSlug = $beginningSlug . $this->_settings[$model->alias]['separator'] . $i++;
			}
		}

		return $currentSlug;
	}
}
