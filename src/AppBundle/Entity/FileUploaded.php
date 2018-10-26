<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * FileUploaded
 *
 * @ORM\Table(name="file_uploaded")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FileUploadedRepository")
 * @Vich\Uploadable
 */
class FileUploaded
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="uploaded_ressources", fileNameProperty="fileUploadedName", size="fileUploadedSize")
     *
     * @var File
     */
    private $fileUploadedFile;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $fileUploadedName;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var integer
     */
    private $fileUploadedSize;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="filesUploaded")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    private $category;

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
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $fileUploadedFile
     *
     * @return FileUploaded
     */
    public function setFileUploadedFile(File $file = null)
    {
        $this->fileUploadedFile = $file;

        if ($file) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    /**
     * @return File|null
     */
    public function getFileUploadedFile()
    {
        return $this->fileUploadedFile;
    }

    /**
     * @param string $fileUploadedName
     *
     * @return FileUploaded
     */
    public function setImageName($fileUploadedName)
    {
        $this->fileUploadedName = $fileUploadedName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFileUploadedName()
    {
        return $this->fileUploadedName;
    }

    /**
     * @param integer $fileUploadedSize
     *
     * @return FileUploaded
     */
    public function setFileUploadedSize($fileUploadedSize)
    {
        $this->fileUploadedSize = $fileUploadedSize;

        return $this;
    }

    /**
     * @return integer|null
     */
    public function getFileUploadedSize()
    {
        return $this->fileUploadedSize;
    }

    /**
     * Set child
     *
     * @param \AppBundle\Entity\Category $category
     *
     * @return FileUploaded
     */
    public function setCategory(\AppBundle\Entity\Category $category = null)
    {
        $this->category = $category;
        $category->addFileUploaded($this);

        return $this;
    }

    /**
     * Get category
     *
     * @return \AppBundle\Entity\Category
     */
    public function getCategory()
    {
        return $this->getCategory();
    }

    /**
     * Set fileUploadedName
     *
     * @param string $fileUploadedName
     *
     * @return FileUploaded
     */
    public function setFileUploadedName($fileUploadedName)
    {
        $this->fileUploadedName = $fileUploadedName;

        return $this;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return FileUploaded
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}
