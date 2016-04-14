<?php
/**
 * Created by PhpStorm.
 * User: jkr
 * Date: 3/23/2016
 * Time: 6:25 PM
 */

namespace RecUp\RecordBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="RecUp\RecordBundle\Repository\SongsRepository")
 * @ORM\Table(name="record")
 */
class Record
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $songName;

    /**
     * @ORM\Column(type="string")
     */
    private $artist;

    /**
     * @ORM\Column(type="string")
     */
    private $genre;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $about;

    /**
     * @ORM\OneToMany(targetEntity="RecordComment", mappedBy="record")
     * @ORM\OrderBy({"createdAt" = "DESC"})
     */
    private $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    /**
     * @ORM\Column(type="boolean")
     */
    private $isPublished = true;

    public function getSongName()
    {
        return $this->songName;
    }

    /**
     * @param mixed $songName
     */
    public function setSongName($songName)
    {
        $this->songName = $songName;
    }

    /**
     * @return mixed
     */
    public function getArtist()
    {
        return $this->artist;
    }

    /**
     * @param mixed $artist
     */
    public function setArtist($artist)
    {
        $this->artist = $artist;
    }

    /**
     * @return mixed
     */
    public function getGenre()
    {
        return $this->genre;
    }

    /**
     * @param mixed $genre
     */
    public function setGenre($genre)
    {
        $this->genre = $genre;
    }

    /**
     * @return mixed
     */
    public function getAbout()
    {
        return $this->about;
    }

    /**
     * @param mixed $about
     */
    public function setAbout($about)
    {
        $this->about = $about;
    }

    public function getUpdatedAt()
    {
        return new \DateTime('-'.rand(0, 100).' days');
    }

    public function setIsPublished($isPublished)
    {
        $this->isPublished = $isPublished;
    }

    /**
     * @return ArrayCollection|RecordComment[]
     */
    public function getComments()
    {
        return $this->comments;
    }

}