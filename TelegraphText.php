<?php

interface LoggerInterface {
    public function logMessage($textError);
    public function lastMessages($count);
}
interface EventListenerInterface {
    public function attachEvent($methodName, $callback);
    public function detouchEvent ($methodName);
}

class TelegraphText
{
    public $title;
    public $text;
    public $author;
    public $published;
    public $slug;

    public function __construct($author, $slug)
    {
        $this->author = $author;
        $this->slug = $slug;
        $this->published = date('d.m.Y H:i:s');
    }
    public function storeText()
    {
        $array = [
            "title" => $this->title,
            "text" => $this->text,
            "author" => $this->author,
            "published" => $this->published,
            "slug" => $this->slug,
        ];

        return file_put_contents($this->slug, serialize($array));
    }
    public function loadText()
    {
        $array = unserialize(file_get_contents($this->slug, false));

        $this->title = $array['title'];
        $this->text = $array['text'];
        $this->author = $array['author'];
        $this->published = $array['published'];

        return $this->text;
    }
    public function editText($title, $text)
    {
        $this->title = $title;
        $this->text = $text;
    }
}

abstract class User implements EventListenerInterface
{
    public $id, $name, $role;
    abstract public function getTextsToEdit();
}
abstract class View
{
    public $storage;

    public function __construct($storage)
    {
        $this->storage = $storage;
    }
    abstract public function displayTextById($id);
    abstract public function displayTextByUrl($url);

    public function attachEvent($methodName, $callback) {

    }
    public function detouchEvent ($methodName) {

    }
}

abstract class Storage implements LoggerInterface, EventListenerInterface
{
    public function logMessage($textError) {

    }
    public function lastMessages($count) {

    }
    public function attachEvent($methodName, $callback) {

    }
    public function detouchEvent ($methodName) {

    }
    abstract public function create(TelegraphText $object);
    abstract public function read($slug);
    abstract public function update($object, $slug);
    abstract public function delete($slug);
    abstract public function list();
}
class FileStorage extends Storage
{
    public $dir = '/xampp/htdocs/welcome/files/';
    public function create(TelegraphText $object)
    {
        $file =  $object;
        $serialize = serialize($file);

        $explodeFile = explode('.', $object->slug);
        $newNameFile = $explodeFile[0];
        $i = 1;

        while(file_exists( $this->dir . $newNameFile . '_' . $i . '.txt')) {
            $i += 1;
        }

        file_put_contents($this->dir . $newNameFile . '_' . $i . '.txt', $serialize);
        return $newNameFile . '_' . $i . '.txt';
    }
    public function read($slug)
    {
        if (file_exists($slug)) {
            $object = unserialize(file_get_contents($slug, false));
            return $object;
        }
        return false;
    }
    public function update($object, $slug)
    {
        if (file_exists($slug)) {
            file_put_contents($slug, serialize(get_object_vars($object)));
            return true;
        }
        return false;
    }
    public function list()
    {
        $content = [];
        $scandir = scandir($this->dir);
        foreach ($scandir as $s) {
            if (!is_dir($s)) {
                if ($s !== '.' & $s !== '..' & $s !== '.git' & $s !== '.idea') {
                    $content[] = unserialize(file_get_contents($this->dir . $s));
                }
            }
        }

        return $content;

    }
    public function delete($slug)
    {
        if (file_exists($slug)) {
            unlink($this->dir . $slug);
            return true;
        }

        return false;
    }
}

$telegraphText = new TelegraphText('Иван', 'text.txt');
$telegraphText->editText("я", "умный");
$telegraphText->storeText();
echo $telegraphText->loadText();

//$test = new FileStorage();
//echo $test->create(new TelegraphText('Иван', 'text.txt'));
$test1 = new FileStorage();
$test1->create(new TelegraphText('Иван', 'text.txt'));
var_dump($test1->list());
//$test1->delete("text.txt_1.txt");
//var_dump($test1->read("text.txt_1.txt"));
//var_dump(file_get_contents('text.txt', '/xampp/htdocs/welcome/'));
