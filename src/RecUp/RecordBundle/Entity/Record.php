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
use RecUp\UserBundle\Entity\User;
use RecUp\UserBundle\Entity\UserProfile;
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
     * @ORM\ManyToOne(targetEntity="RecUp\UserBundle\Entity\UserProfile", inversedBy="songs")
     * @ORM\OrderBy({"createdAt" = "DESC"})
     */
    private $username;
    

    /**
     * @ORM\Column(type="datetime")
     * 
     * @var \DateTime
     */

    private $updatedAt;

    /**
     * @ORM\ManyToMany(targetEntity="RecUp\UserBundle\Entity\User")
     */
    private $likes;

    /**
     * @ORM\ManyToMany(targetEntity="RecUp\UserBundle\Entity\User"))
     * @ORM\JoinTable(name="user_favorite")
     */
    private $favorites;


    /**
     * @ORM\Column(type="boolean")
     */
    private $isDownloadable;

    /**
     * @return mixed
     */
    public function getIsDownloadable()
    {
        return $this->isDownloadable;
    }

    /**
     * @param mixed $isDownloadable
     */
    public function setIsDownloadable($isDownloadable)
    {
        $this->isDownloadable = $isDownloadable;
    }
    
    
    
    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }
    
    
    /**
     * @param mixed $userProfile
     */
    public function setUsername(UserProfile $userProfile)
    {
       $this->username = $userProfile;  
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
    
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
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
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
        $this->likes = new ArrayCollection();
        $this->favorites = new ArrayCollection();
    }
    
    public function getLikes()
    {
        return $this->likes;
    }
    
    public function getFavorites()
    {
        return $this->favorites;
    }
    
    public function hasLikes(User $user)
    {
        return $this->getLikes()->contains($user);
    }
    
    public function hasFavorites(User $user)
    {
        return $this->getFavorites()->contains($user);
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

//    public function getUpdatedAt()
//    {
//        return new \DateTime('-'.rand(0, 100).' days');
//    }

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