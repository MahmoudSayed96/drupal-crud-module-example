<?php

/**
 * @file
 * @Contains Drupal\crud\Controller\CRUDController.
 */

namespace Drupal\crud\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\file\Entity\File;

/**
 * Implement CRUD class operations.
 */
class CRUDController extends ControllerBase
{
    public function index()
    {
        //create table header
        $header_table = [
            'id' => $this->t('ID'),
            'first_name' => $this->t('first name'),
            'last_name' => $this->t('last name'),
            'email' => $this->t('Email'),
            'phone' => $this->t('phone'),
            'view' => $this->t('View'),
            'delete' => $this->t('Delete'),
            'edit' => $this->t('Edit'),
        ];

        // get data from database
        $query = \Drupal::database()->select('crud_table', 'm');
        $query->fields('m', ['id', 'first_name', 'last_name', 'email', 'message']);
        $results = $query->execute()->fetchAll();
        $rows = [];
        foreach ($results as $data) {
            $url_delete = Url::fromRoute('crud.delete_form', ['id' => $data->id], []);
            $url_edit = Url::fromRoute('crud.add_form', ['id' => $data->id], []);
            $url_view = Url::fromRoute('crud.show_data', ['id' => $data->id], []);
            $linkDelete = Link::fromTextAndUrl('Delete', $url_delete);
            $linkEdit = Link::fromTextAndUrl('Edit', $url_edit);
            $linkView = Link::fromTextAndUrl('View', $url_view);

            //get data
            $rows[] = [
                'id' => $data->id,
                'first_name' => $data->first_name,
                'last_name' => $data->last_name,
                'email' => $data->email,
                'message' => $data->message,
                // 'phone' => $data->phone,
                'view' => $linkView,
                'delete' => $linkDelete,
                'edit' =>  $linkEdit,
            ];
        }
        // render table
        $form['table'] = [
            '#type' => 'table',
            '#header' => $header_table,
            '#rows' => $rows,
            '#empty' => $this->t('No data found'),
        ];
        return $form;
    }

    public function show(int $id)
    {
        $conn = Database::getConnection();

        $query = $conn->select('crud_table', 'm')
            ->condition('id', $id)
            ->fields('m');
        $data = $query->execute()->fetchAssoc();
        $full_name = $data['first_name'] . ' ' . $data['last_name'];
        $email = $data['email'];
        // $phone = $data['phone'];
        $message = $data['message'];

        $file = File::load($data['fid']);
        $picture = $file->createFileUrl();

        return [
            '#type' => 'markup',
            '#markup' => "<h1>$full_name</h1><br>
                          <img src='$picture' width='100' height='100' /> <br>
                          <p>$email</p>
                          <p>$message</p>"
        ];
    }
}
