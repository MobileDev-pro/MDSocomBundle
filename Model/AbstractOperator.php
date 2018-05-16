<?php

namespace MD\SocomBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Operator
 *
 * @ORM\MappedSuperclass
 */
abstract class AbstractOperator implements OperatorInterface
{
    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $company;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank(message = "Veuillez renseigner une adresse")
     */
    protected $address;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank(message = "Veuillez renseigner une ville")
     */
    protected $city;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank(message = "Veuillez renseigner le code postal du détenteur")
     */
    protected $zipCode;

    /**
     * @ORM\Column(type="string", nullable=false, unique=true)
     * @Assert\NotBlank()
     */
    protected $email;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $contact;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $phone;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="create")
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="create")
     * @gedmo\Timestampable(on="update")
     */
    protected $updatedAt;

    protected $user;

    /**
     * @param string $company
     * @return OperatorInterface
     */
    public function setCompany(string $company): OperatorInterface
    {
        $this->company = $company;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getCompany(): ?string
    {
        return $this->company;
    }
    /**
     * @param string $zipCode
     * @return OperatorInterface
     */
    public function setZipCode(string $zipCode): OperatorInterface
    {
        $zc = (int) str_replace(' ', '', $zipCode);

        if (strlen($zc) == 4) {
            $zc = (string)"0" . $zc;
        }

        $cp = substr_replace($zc, ' ', 2, 0);
        $this->zipCode = $this->formatZipCode($cp);

        return $this;
    }

    /**
     * @return null|string
     */
    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    /**
     * @param string $address
     * @return $this
     */
    public function setAddress(string $address): OperatorInterface
    {
        $this->address = $address;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @param string $city
     * @return OperatorInterface
     */
    public function setCity(string $city): OperatorInterface
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @param string $email
     * @return OperatorInterface
     */
    public function setEmail(string $email): OperatorInterface
    {
        $this->email = strtolower( $email );

        return $this;
    }

    /**
     * @return null|string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $contact
     * @return OperatorInterface
     */
    public function setContact(string $contact): OperatorInterface
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getContact(): ?string
    {
        return $this->contact;
    }

    /**
     * @param string $phone
     * @return OperatorInterface
     */
    public function setPhone(string $phone): OperatorInterface
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param \DateTime $createdAt
     * @return OperatorInterface
     */
    public function setCreatedAt(\DateTime $createdAt): OperatorInterface
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $updatedAt
     * @return OperatorInterface
     */
    public function setUpdatedAt(\DateTime $updatedAt): OperatorInterface
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @return \Countable
     */
    abstract function getUsers(): \Countable;

    /**
     * @param UserInterface $user
     * @return OperatorInterface
     */
    abstract public function addUser(UserInterface $user): OperatorInterface;

    /**
     * @param UserInterface $user
     * @return OperatorInterface
     */
    abstract function removeUser(UserInterface $user): OperatorInterface;

    /**
     * @param string $zipCode
     * @return string
     */
    private function formatZipCode(string $zipCode): string
    {
        $zip = trim($zipCode);
        if (preg_match("/[0-9]{1}(?:A|B|[0-9])[\s][0-9]{3}/i",$zip)) {
            return $zip;
        }

        if (preg_match("/[0-9]{5}/",$zip)) {
            $arr = str_split($zip, 2);
            $code = $arr[0] . ' ' . $arr[1] . $arr[2];

            return trim($code);
        }

        return $zip;
    }
}
