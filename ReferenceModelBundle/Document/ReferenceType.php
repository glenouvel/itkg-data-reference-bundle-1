<?php

namespace Itkg\ReferenceModelBundle\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Gedmo\Blameable\Traits\BlameableDocument;
use Gedmo\Timestampable\Traits\TimestampableDocument;
use Itkg\ReferenceInterface\Model\ReferenceTypeInterface;
use OpenOrchestra\Mapping\Annotations as ORCHESTRA;
use OpenOrchestra\ModelInterface\Model\FieldTypeInterface;
use OpenOrchestra\ModelInterface\Model\TranslatedValueInterface;

/**
 * Description of ReferenceType
 *
 * @ODM\Document(
 *   collection="reference_type",
 *   repositoryClass="Itkg\ReferenceModelBundle\Repository\ReferenceTypeRepository"
 * )
 */
class ReferenceType implements ReferenceTypeInterface
{
    use BlameableDocument;
    use TimestampableDocument;

    /**
     * @var string $id
     *
     * @ODM\Id
     */
    protected $id;

    /**
     * @var string $referenceTypeId
     *
     * @ODM\Field(type="string")
     * @ORCHESTRA\Search(key="reference_type_id")
     */
    protected $referenceTypeId;

    /**
     * @var ArrayCollection $names
     *
     * @ODM\EmbedMany(targetDocument="OpenOrchestra\ModelInterface\Model\TranslatedValueInterface")
     * @ORCHESTRA\Search(key="name", type="translatedValue")
     */
    protected $names;

    /**
     * @var int $version
     *
     * @ODM\Field(type="int")
     */
    protected $version = 1;

    /**
     * @var boolean $deleted
     *
     * @ODM\Field(type="boolean")
     */
    protected $deleted = false;

    /**
     * @var ArrayCollection $fields
     *
     * @ODM\EmbedMany(targetDocument="OpenOrchestra\ModelInterface\Model\FieldTypeInterface")
     */
    protected $fields;

    /**
     * @var string $template
     *
     * @ODM\Field(type="string")
     */
    protected $template;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->initializeCollections();
    }

    /**
     * @param string $referenceTypeId
     */
    public function setReferenceTypeId($referenceTypeId)
    {
        $this->referenceTypeId = $referenceTypeId;
    }

    /**
     * @return string
     */
    public function getReferenceTypeId()
    {
        return $this->referenceTypeId;
    }

    /**
     * @param boolean $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

    /**
     * @return boolean
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param FieldTypeInterface $field
     */
    public function addFieldType(FieldTypeInterface $field)
    {
        $this->fields->add($field);
    }

    /**
     * @param FieldTypeInterface $fields
     */
    public function setFields(FieldTypeInterface $fields)
    {
        $this->fields = $fields;
    }

    /**
     * @param FieldTypeInterface $field
     */
    public function removeFieldType(FieldTypeInterface $field)
    {
        $this->fields->removeElement($field);
    }

    /**
     * @return ArrayCollection
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param TranslatedValueInterface $name
     */
    public function addName(TranslatedValueInterface $name)
    {
        $this->names->add($name);
    }

    /**
     * @param TranslatedValueInterface $name
     */
    public function removeName(TranslatedValueInterface $name)
    {
        $this->names->removeElement($name);
    }

    /**
     * @param string $language
     *
     * @return string
     */
    public function getName($language = 'en')
    {
        $choosenLanguage = $this->names->filter(function (TranslatedValueInterface $translatedValue) use ($language) {
            return $language == $translatedValue->getLanguage();
        });

        if ($choosenLanguage->first()) {
            return $choosenLanguage->first()->getValue();
        }

        return '';
    }

    /**
     * @return ArrayCollection
     */
    public function getNames()
    {
        return $this->names;
    }

    /**
     * @param int $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getReferenceTypeId();
    }

    /**
     * @return array
     */
    public function getTranslatedProperties()
    {
        return array(
            'getNames'
        );
    }

    /**
     * initialize collections
     */
    protected function initializeCollections()
    {
        $this->fields = new ArrayCollection();
        $this->names = new ArrayCollection();
    }

    /**
     * @param string $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }
}
