<?php

namespace Modules\Iform\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Iform\Models\Form;

class InitialFormsSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    if (Form::count() === 0) {
      $formRepository = app('Modules\Iform\Repositories\FormRepository');
      $blockRepository = app("Modules\Iform\Repositories\BlockRepository");
      $fieldRepository = app("Modules\Iform\Repositories\FieldRepository");
      $form = $formRepository->create([
        'es' => [
          'title' => 'Formulario Contacto',
        ],
        'en' => [
          'title' => 'Contact Form',
        ],
        'system_name' => 'contact_form',
        'active' => true,
      ]);

      $block = $blockRepository->create([
        'form_id' => $form->id,
      ]);

      $fieldRepository->create([
        'form_id' => $form->id,
        'block_id' => $block->id,
        'es' => [
          'label' => 'Nombre Completo',
          'placeholder' => 'Nombre Completo',
        ],
        'en' => [
          'label' => 'Full Name',
          'placeholder' => 'Full Name',
        ],
        'type_id' => 1,
        'system_name' => 'full_name',
        'required' => true,
      ]);

      $fieldRepository->create([
        'form_id' => $form->id,
        'block_id' => $block->id,
        'es' => [
          'label' => 'Correo electronico',
          'placeholder' => 'Correo electronico',
        ],
        'en' => [
          'label' => 'Email',
          'placeholder' => 'Email',
        ],
        'type_id' => 4,
        'system_name' => 'email',
        'required' => true,
      ]);

      $fieldRepository->create([
        'form_id' => $form->id,
        'block_id' => $block->id,
        'es' => [
          'label' => 'Telefono',
          'placeholder' => 'Telefono',
        ],
        'en' => [
          'label' => 'Phone',
          'placeholder' => 'Phone',
        ],
        'type_id' => 10,
        'system_name' => 'telephone',
        'required' => true,
      ]);

      $fieldRepository->create([
        'form_id' => $form->id,
        'block_id' => $block->id,
        'es' => [
          'label' => 'Mensaje',
          'placeholder' => 'Mensaje...',
        ],
        'en' => [
          'label' => 'Message',
          'placeholder' => 'Message...',
        ],
        'type_id' => 2,
        'system_name' => 'message',
        'required' => true,
      ]);
    }
  }
}