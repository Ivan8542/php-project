<?php

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

abstract class User
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
}

abstract class Storage
{
    abstract public function create(TelegraphText $object);
    abstract public function read($slug);
    abstract public function update($object, $slug);
    abstract public function delete($slug);
    abstract public function list();
}
class FileStorage extends Storage
{
    public $dir = '/xampp/htdocs/welcome/';
    public function create(TelegraphText $object)
    {
        $file =  $object;
        $serialize = serialize($file);

        $i = 1;
        $slug = $object->slug . '_' . $i . '.txt';
        while(file_exists($this->dir . $slug)) {
            $i += 1;
        }

        file_put_contents($slug, $serialize);
        return $slug;
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
        $scandir = scandir($this->dir);
        foreach ($scandir as $s) {

        }

    }
    public function delete($slug)
    {
        if (file_exists($slug)) {
            unlink($slug);
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
var_dump($test1->read("text.txt_1.txt"));


