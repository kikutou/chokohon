<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\I18n\Date;
use App\Form\SearchFuriruForm;
use App\Form\SearchMerukariForm;
use App\Form\SearchRakumaForm;
use App\Model\Table\MerukariTable;
use Cake\ORM\TableRegistry;

class BooksController extends AppController
{

  public function index()
  {
    $this->loadModel('Books');

    $books = $this->paginate($this->Books);

    $this->set(compact('books'));
    $this->set('_serialize', ['books']);

  }


  public function view($id = null)
  {


  }


  public function add()
  {
    $this->loadModel('Books');
    $this->loadModel('Furiru');
    $this->loadModel('Merukari');
    $this->loadModel('Rakuma');

    $book = $this->Books->newEntity();
    $furiru = $this->Furiru->newEntity();
    $merukari = $this->Merukari->newEntity();
    $rakuma = $this->Rakuma->newEntity();


    if ($this->request->is('post')) {
        $book = $this->Books->patchEntity($book, $this->request->getData());

        $furiru->key_words = $this->request->getData("book_name");
        $merukari->key_words = $this->request->getData("book_name");
        $rakuma->key_words = $this->request->getData("book_name");


        $book->furiru = $furiru;
        $book->merukari = $merukari;
        $book->rakuma = $rakuma;


        if ($this->Books->save($book)) {

            $this->Flash->success(__('登録は成功しました.'));

            return $this->redirect(['action' => 'index']);
        }
        $this->Flash->error(__('登録は失敗しました、もう一度試してください.'));
    }
    $this->set(compact('book'));
    $this->set('_serialize', ['book']);
  }


  public function edit($id = null)
  {
    $book = $this->Books->get($id, [
        'contain' => [ ]
    ]);

    if ($this->request->is(['post','put','patch'])) {
        $book = $this->Books->patchEntity($book, $this->request->getData());

        if ($this->Books->save($book)) {
            $this->Flash->success(__('編集は成功しました.'));

            return $this->redirect(['action' => 'index']);
        }
        $this->Flash->error(__('編集は失敗しました、もう一度試してください.'));
    }
    $this->set(compact('book'));
    $this->set('_serialize', ['book']);

  }


  public function delete($id = null)
  {
    $this->request->allowMethod(['post', 'delete']);
    $book = $this->Books->get($id);
    if ($this->Books->delete($book)) {
        $this->Flash->success(__('削除しました.'));
    } else {
        $this->Flash->error(__('削除は失敗しました、もう一度試してください.'));
    }

    return $this->redirect(['action' => 'index']);

  }
  public function view_rakuma($id = null) {

        $queryData = $this->request->getQuery();
        $id = isset($id) ? $id : $id = $queryData["id"];
        if (!isset($queryData["key_word"]) || $queryData["key_word"] == NULL) {
            $this->Flash->error(__('検索キーワードを入力してください'));
        }
        if (isset($queryData["form_name"]) && $queryData["form_name"] == "update_rules_form") {
            $rulesTable = TableRegistry::get('RakumaRules');
            $rules = $rulesTable->get(["book_id" => $id]);


            $rules = $rulesTable->patchEntity($rules, $queryData);

            if ($rulesTable->save($rules)) {
                $this->Flash->success(__('検索条件が更新されました'));
            } else {
                $this->Flash->error(__('システムエラー、保存できません'));

            }
            if ($id) {
                $rakuma = new RakumaTable;
                $rakuma = $rakuma->get_books($id);
            }
            $this->set(compact('rakuma'));
            $this->set(compact('id'));
            $this->set(compact('queryData'));

    }
}
