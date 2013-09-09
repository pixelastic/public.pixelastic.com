<?php
/**
 *	SluggableBehavior
 *	Automatically generate a slug value based on the model title
 **/
class SluggableBehavior extends ModelBehavior  {

	/**
	 *	setup
	 *	Loaded when applied to the model, will add a slug field in the admin panel
	 **/
	function setup(&$model, $config = array()) {
		$model->adminSettings = Set::merge(
			array(
				'fields' => array(
					'slug' => array(
						'label' => __d('caracole', 'Slug', true),
						'help' => __d('caracole','Type in the slug you want for this item. The slug will be displayed in the url and help SEO. If in doubt, keep empty.', true),
						'advanced' => true
					)
				)
			),
			$model->adminSettings
		);
	}

	/**
	 *	beforeSave
	 *	Generating a slug
	 **/
	function beforeValidate(&$model) {
		// If we're not updating the displayField, we won't change the slug
		if (empty($model->data[$model->alias][$model->displayField])) {
			return true;
		}
		//If a slug is defined, we use it, otherwise we use the displayField
		$originalSlug = (!empty($model->data[$model->alias]['slug'])) ? $model->data[$model->alias]['slug'] : $model->data[$model->alias][$model->displayField];
		// We slugify it
		$slug = CaracoleInflector::slug($originalSlug);
		// If it is empty (can be if only contain common words), we do a less aggressive slug
		if (empty($slug)) {
			$slug = strtolower(Inflector::slug($originalSlug, '-'));
		}
		// If it is still empty, we use a random hash instead
		if (empty($slug)) {
			$slug = md5($model->data[$model->alias][$model->displayField]);
		}
		// We add it to the list of fields to update
		$model->data[$model->alias]['slug'] = $slug;

		parent::beforeValidate($model);
		return true;
	}



}
