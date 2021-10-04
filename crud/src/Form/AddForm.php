<?php

/**
 * @file
 * @Contains Drupal\crud\Form\AddForm.
 */

namespace Drupal\crud\Form;

use Drupal\Core\Database\Database;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\file\Entity\File;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Add form implementation.
 */
class AddForm extends FormBase {
    /**
     * (@inheritdoc)
     */
    public function getFormId() {
        return 'crud_form_id';
    }

    /**
     * (@inheritdoc)
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        $conn = Database::getConnection();
        $data = [];
        if (isset($_GET['id'])) {
            $query = $conn->select('crud_table', 'm')
                ->condition('id', $_GET['id'])
                ->fields('m');
            $data = $query->execute()->fetchAssoc();
        }

        $form['first_name'] = [
            '#type' => 'textfield',
            '#title' => $this->t('First Name'),
            '#default_value' => (isset($data['first_name'])) ? $data['first_name'] : '',
            '#required' => TRUE,
            '#wrapper_attributes' => ['class' => 'col-md-6 col-12'],
        ];
        $form['last_name'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Last Name'),
            '#default_value' => (isset($data['last_name'])) ? $data['last_name'] : '',
            '#required' => TRUE,
            '#wrapper_attributes' => ['class' => 'col-md-6 col-12'],
        ];
        $form['email'] = [
            '#type' => 'email',
            '#title' => $this->t('First Name'),
            '#default_value' => (isset($data['email'])) ? $data['email'] : '',
            '#required' => TRUE,
            '#placeholder' => 'mail@example.com',
            '#wrapper_attributes' => ['class' => 'col-md-6 col-12'],
        ];
        $form['picture'] = [
            '#type' => 'managed_file',
            '#title' => $this->t('Picture'),
            '#description' => $this->t('Choosier Image gif png jpg jpeg'),
            '#required' => (isset($_GET['id'])) ? FALSE : TRUE,
            '#upload_location' => 'public://images/',
            '#upload_validators' => [
                'file_validate_extension' => ['png jpeg jpg'],
            ]
        ];
        // $form['phone'] = [
        //     '#type' => 'tel',
        //     '#title' => $this->t('phone'),
        //     '#required' => true,
        //     '#default_value' => ' ',
        //     '#wrapper_attributes' => ['class' => 'col-md-6 col-xs-12']
        // ];
        $form['select'] = [
            '#type' => 'select',
            '#title' => $this
                ->t('Select element'),
            '#options' => [
                '1' => $this
                    ->t('One'),
                '2' => [
                    '2.1' => $this
                        ->t('Two point one'),
                    '2.2' => $this
                        ->t('Two point two'),
                ],
                '3' => $this
                    ->t('Three'),
            ],
            '#default_value' => (isset($data['select'])) ? $data['select'] : '',
            '#wrapper_attributes' => ['class' => 'col-md-6 col-xs-12'],
        ];
        $form['message'] = [
            '#type' => 'textarea',
            '#title' => $this->t('message'),
            '#required' => true,
            '#default_value' => (isset($data['message'])) ? $data['message'] : '',
            '#wrapper_attributes' => ['class' => 'col-md-6 col-xs-12'],
        ];
        $form['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('save'),
            '#buttom_type' => 'primary',
        ];

        return $form;
    }

    /**
     * (@inheritdoc)
     */
    public function validateForm(array &$form, FormStateInterface $form_state) {
        if (is_numeric($form_state->getValue('first_name'))) {
            $form_state->setErrorByName('first_name', $this->t('Error, The First Name Must Be A String'));
        }
    }

    /**
     * (@inheritdoc)
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $picture = $form_state->getValue('picture');
        $data = [
            'first_name'    => $form_state->getValue('first_name'),
            'last_name'     => $form_state->getValue('last_name'),
            'email'         => $form_state->getValue('email'),
            // 'phone'         => $form_state->getValue('phone'),
            // 'select'        => $form_state->getValue('select'),
            'message'       => $form_state->getValue('message'),
        ];

        if (!is_null($picture[0])) {
            $data += [
                'fid' => $picture[0],
            ];
        }

        if (isset($_GET['id'])) {
            // update data in database
            \Drupal::database()->update('crud_table')->fields($data)->condition('id', $_GET['id'])->execute();
        } else {
            // Insert data to database.
            \Drupal::database()->insert('crud_table')->fields($data)->execute();
        }
        if (!is_null($picture[0])) {
            // Save file as Permanent.
            $file = File::load($picture[0]);
            $file->setPermanent();
            $file->save();
        }

        // Show message and redirect to list page.
        \Drupal::messenger()->addStatus($this->t('Successfully saved'));
        $url = new Url('crud.display_data');
        $response = new RedirectResponse($url->toString());
        $response->send();
    }
}
