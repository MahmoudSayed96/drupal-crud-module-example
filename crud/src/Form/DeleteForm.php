<?php
/**
* @file
* @Contains Drupal\crud\Form\DeleteForm.
*/
namespace Drupal\crud\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Url;

/**
 * Class DeleteForm
 * @package Drupal\crud\Form
 */
class DeleteForm extends ConfirmFormBase {
    public $id;

    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'delete_form';
    }

    public function getQuestion() {
        return $this->t('Delete data');
    }

    public function getCancelUrl() {
        return new Url('crud.display_data');
    }

    public function getDescription() {
        return $this->t('Do you want to delete data number %id ?', ['%id' => $this->id]);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfirmText() {
        return $this->t('Delete it!');
    }

    /**
     * {@inheritdoc}
     */
    public function getCancelText() {
        return $this->t('Cancel');
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state, $id = NULL) {

        $this->id = $id;
        return parent::buildForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state) {
        parent::validateForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $query = \Drupal::database();
        $query->delete('crud_table')
            ->condition('id', $this->id)
            ->execute();
        \Drupal::messenger()->addStatus('Successfully deleted.');
        $form_state->setRedirect('crud.display_data');
    }
}
