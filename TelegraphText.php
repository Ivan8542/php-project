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

$telegraphText = new TelegraphText('Иван', 'text.txt');
$telegraphText->editText("я", "умный");
$telegraphText->storeText();
echo $telegraphText->loadText();

