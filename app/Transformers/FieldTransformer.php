<?php

namespace Modules\Iform\Transformers;

use Imagina\Icore\Transformers\CoreResource;
use Illuminate\Support\Str;

class FieldTransformer extends CoreResource
{
  /**
   * Attribute to exclude relations from transformed data
   * @var array
   */
  protected array $excludeRelations = [];

  /**
   * Method to merge values with response
   *
   * @return array
   */
  public function modelAttributes($request): array
  {
    return [
      'type' => $this->type,
      'dynamicField' => $this->buildDynamicField($this)
    ];
  }

  public function buildDynamicField($field): array
  {

    //simplifying the type value variable
    $fieldType = $field->type["value"] ?? "";

    $fieldResponse = [
      'type' => match (true) {
        in_array($fieldType, ['text', 'textarea', 'number', 'email', 'phone']) => 'input',
        $fieldType === 'file' => 'media',
        $fieldType === 'selectmultiple' => 'select',
        default => $fieldType,
      },
      'name' => $fieldType === 'file' ? "mediasSingle" : $field->name,
      'required' => (bool)$field->required,
      'value' => match (true) {
        in_array($fieldType, ['selectmultiple', 'radio']) => [],
        $fieldType === 'checkbox' => false,
        default => '',
      },
      'colClass' => "col-12 col-sm-" . ($field->width ?? '12'),
      'props' => [
        'label' => $field->label,
        'entity' => $field->options["entity"] ?? "",
        'multiple' => $fieldType === 'selectmultiple'
      ]
    ];

    // Add input type for specific fields
    if (in_array($fieldType, ['number', 'email'], true)) {
      $fieldResponse['props']['type'] = $fieldType;
    }

    //Options for ['selectmultiple', 'select', 'radio', 'treeSelect'] field types
    if (in_array($fieldType, ['selectmultiple', 'select', 'radio', 'treeSelect'], true)) {
      // loadOptions from DB if available
      if (!empty($field->options['loadOptions'] ?? null)) {
        $fieldResponse['loadOptions'] = $field->options['loadOptions'];
      }

      //Added options in the format label: value, expected for the frontend standard
      foreach ($field->fieldOptions as $option) {
        $label = $option->name ?? (string) $option;
        $fieldResponse['props']['options'][] = [
          'label' => $label,
          'value' => $label,
        ];
      }

    }

    //Add options for textarea
    if ($fieldType == "textarea") {
      $fieldResponse['props']['type'] = "textarea";
      $fieldResponse['props']['rows'] = 3;
    }

    return $fieldResponse;
  }
}
