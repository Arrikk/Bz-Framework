<?php

namespace Module\Form;

use Core\Http\Res;
use Random\Engine\Secure;

class FormBuilder
{
    private $required = ['name', 'type'];
    private $name;
    private $type;
    private $form;
    private array $error;
    public function __construct($formParams)
    {
        $this->form = array_filter((array) $formParams, [$this, 'formParams'], ARRAY_FILTER_USE_BOTH);
        if (!empty($this->error)) Res::status(400)::error($this->error);
    }

    /**
     * Validate Required Params for FROM
     * @param object $form
     * @return string $key
     */
    public function formParams($form, $key)
    {
        $form->name = $key;
        if (!isset($form->type) || isset($form->type) && $form->type == '')
            $this->error[$key] = (object)[
                'type' => [
                    'required' => true,
                    'is_provided' => isset($form->type),
                ],
                'name' => [
                    'required' => true,
                    'is_provided' => isset($form->name),
                ],
            ];
        foreach ($form as $key => $val) :
            if (is_bool($form->{$key})) continue;
            $form->{$key} = Secure($val);
        endforeach;

        $this->name = $form->name ?? '';
        $this->type = $form->type ?? '';
        return $form;
    }

    public function form($form, $title = RANDOM, $status = DRAFT)
    {
        return [
            'form_accessibility' => $form->accessibility,
            'form_template' => $form->template,
            'form_status' => $status,
            'form_title' => $title,
            'form_name' => $this->name,
            'form_type' => $this->type,
            'form_attributes' => json_encode($this->form),
            'form_slug' => preg_replace('/[^\da-z]+/i', '-', $title),
        ];
    }
}
