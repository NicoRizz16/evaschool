<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Category
 *
 * @ORM\Table(name="category")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CategoryRepository")
 */
class Category
{
    const CRPE_SECTION = 1;
    const ECOLE_SECTION = 2;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="section", type="integer")
     */
    private $section;

    /**
     * @var string
     *
     * @ORM\Column(name="color", type="string", length=255)
     */
    private $color;

    /**
     * One category has Many childrens.
     * @ORM\OneToMany(targetEntity="Category", mappedBy="parent", cascade={"remove"})
     */
    private $children;

    /**
     * Many childrens have One parent category.
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true)
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\FileUploaded", mappedBy="category", cascade={"remove"})
     */
    private $filesUploaded;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->filesUploaded = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Category
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set section
     *
     * @param integer $section
     *
     * @return Category
     */
    public function setSection($section)
    {
        $this->section = $section;

        return $this;
    }

    /**
     * Get section
     *
     * @return int
     */
    public function getSection()
    {
        return $this->section;
    }

    /**
     * Set color
     *
     * @param string $color
     *
     * @return Category
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color
     *
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Add child
     *
     * @param \AppBundle\Entity\Category $child
     *
     * @return Category
     */
    public function addChild(\AppBundle\Entity\Category $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove child
     *
     * @param \AppBundle\Entity\Category $child
     */
    public function removeChild(\AppBundle\Entity\Category $child)
    {
        $this->children->removeElement($child);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set parent
     *
     * @param \AppBundle\Entity\Category $parent
     *
     * @return Category
     */
    public function setParent(\AppBundle\Entity\Category $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \AppBundle\Entity\Category
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add FileUploaded
     *
     * @param \AppBundle\Entity\FileUploaded $fileUploadedFile
     *
     * @return Category
     */
    public function addFileUploaded(\AppBundle\Entity\FileUploaded $fileUploadedFile)
    {
        $this->filesUploaded[] = $fileUploadedFile;

        return $this;
    }

    /**
     * Remove FileUploaded
     *
     * @param \AppBundle\Entity\FileUploaded $fileUploadedFile
     */
    public function removeFileUploaded(\AppBundle\Entity\FileUploaded $fileUploadedFile)
    {
        $this->filesUploaded->removeElement($fileUploadedFile);
    }

    /**
     * Get filesUploaded
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFilesUploaded()
    {
        return $this->filesUploaded;
    }

    /**
     * Add filesUploaded
     *
     * @param \AppBundle\Entity\FileUploaded $filesUploaded
     *
     * @return Category
     */
    public function addFilesUploaded(\AppBundle\Entity\FileUploaded $filesUploaded)
    {
        $this->filesUploaded[] = $filesUploaded;

        return $this;
    }

    /**
     * Remove filesUploaded
     *
     * @param \AppBundle\Entity\FileUploaded $filesUploaded
     */
    public function removeFilesUploaded(\AppBundle\Entity\FileUploaded $filesUploaded)
    {
        $this->filesUploaded->removeElement($filesUploaded);
    }
}
