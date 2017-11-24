<?php

namespace Model;

use Library\PdoAwareTrait;
use Model\Entity\Banner;


class BannersRepository
{

    use PdoAwareTrait;

    public function addBanner($img,$link)
    {
        $sth = $this->pdo->prepare('INSERT INTO `banners` VALUES (null, :img, :link)');
        $sth->execute([
            'img' => $img,
            'link' =>$link,
        ]);

    }


    public function findAllBanners()
    {
        $collection = [];
        $sth = $this->pdo->query('SELECT * FROM banners');
        while ($res = $sth->fetch(\PDO::FETCH_ASSOC)) {
            $banner = (new Banner())
                ->setId($res['id'])
                ->setImg($res['img'])
                ->setLink($res['link']);

            $collection[] = $banner;
        }
        return $collection;
    }

    public function findBannerById($id)
    {

        $sth = $this->pdo->query("SELECT * FROM banners WHERE `id`=$id");
        $res = $sth->fetch(\PDO::FETCH_ASSOC);
        $banner = (new Banner())
                ->setId($res['id'])
                ->setImg($res['img'])
                ->setLink($res['link']);

        return $banner;
    }
    public function findChosenBannerIdByPosition($position)
    {

        $sth = $this->pdo->query("SELECT * FROM `chosen_banners` WHERE `position`='$position' ");
        $res = $sth->fetch(\PDO::FETCH_ASSOC);
        $banners_id = (new Banner())
                ->setId($res['banner\'s_id']);
        return $banners_id;
    }


    public function UpdateChosenBanners($position,$id)
    {
        $sth = $this->pdo->prepare("UPDATE `chosen_banners` SET `banner's_id` =:id WHERE `position` = :position;");
        $sth->execute([
            'id' => $id,
            'position' => $position
        ]);
    }


}
