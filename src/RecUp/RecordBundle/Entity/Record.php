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
use Symfony\Component\Validator\Constraints\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity(repositoryClass="RecUp\RecordBundle\Repository\SongsRepository")
 * @ORM\Table(name="record")
 * @Assert\Callback(methods={"validate"})
 * @Vich\Uploadable()
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

    /**
     * @Vich\UploadableField(mapping="record_song", fileNameProperty="songName")
     * @var File
     */
    private $songFile;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @return Record
     */
    public function setSongFile($song = null)
    {
        $this->songFile = $song;

        if ($song){
            $this->updatedAt = new \DateTime('now');
        }

        return $this;
    }

    /**
     * @return File
     *
     */
    public function getSongFile()
    {
        return $this->songFile;
    }

    /**
     * @param string $songName
     * 
     * @return Record
     */
    public function setSongName($songName)
    {
        $this->songName = $songName;

        return $this;
    }

    public function getSongName()
    {
        return $this->songName;
    }



    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    /**
     * @ORM\Column(type="boolean")
     */
    private $isPublished = true;

//    public function getSongName()
//    {
//        return $this->songName;
//    }

//    /**
//     * @param mixed $songName
//     */
//    public function setSongName($songName)
//    {
//        $this->songName = $songName;
//    }

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

    /**
     * @param ExecutionContextInterface $context
     */
    public function validate(ExecutionContextInterface $context)
    {
        if (! in_array($this->getSongFile()->getMimeType(), array(
            'audio/ogg',
            'audio/mpeg',
            'audio/x-wav',
        ))) {
            $context
                ->buildViolation('Wrong audio type! allowed (.mp3,.ogg, or .wav)')
                ->atPath('songFile')
                ->addViolation()
            ;
        }
    }
}