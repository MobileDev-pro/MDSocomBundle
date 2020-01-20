<?php

namespace MD\SocomBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Accessor;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Operator
 *
 * @ORM\MappedSuperclass
 * @UniqueEntity("email")
 */
abstract class AbstractOperator implements OperatorInterface
{
    /**
     * @ORM\Column(type="string", nullable=false)
     * @Accessor(getter="getCompany",setter="setCompany")
     * @Type("string")
     */
    protected $company;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank(message = "Veuillez renseigner une adresse")
     * @Accessor(getter="getAddress",setter="setAddress")
     * @Type("string")
     */
    protected $address;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank(message = "Veuillez renseigner une ville")
     * @Accessor(getter="getCity",setter="setCity")
     * @Type("string")
     */
    protected $city;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank(message = "Veuillez renseigner le code postal")
     * @Accessor(getter="getZipCode",setter="setZipCode")
     * @Type("string")
     */
    protected $zipCode;

    /**
     * @ORM\Column(type="string", nullable=false, unique=true)
     * @Assert\NotBlank()
     * @Accessor(getter="getEmail",setter="setEmail")
     * @Type("string")
     */
    protected $email;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Accessor(getter="getContact",setter="setContact")
     * @Type("string")
     */
    protected $contact;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Accessor(getter="getPhone",setter="setPhone")
     * @Type("string")
     */
    protected $phone;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Accessor(getter="getCreatedAt",setter="setCreatedAt")
     * @Type("datetime")
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Accessor(getter="getUpdatedAt",setter="setUpdatedAt")
     * @Type("datetime")
     */
    protected $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity="UserInterface", mappedBy="operator", cascade={"all"})
     * @Accessor(getter="getUsers",setter="setUsers")
     * @Assert\Valid
     */
    protected $users;

    /**
     * @ORM\Column(type="string", nullable=false, options={"default":"fi-360deg"})
     * @Accessor(getter="getApplication",setter="setApplication")
     * @Type("string")
     */
    protected $application;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $attestationCapacite;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $siret;

    public function __construct()
    {
        $now = new \DateTime();

        $this->createdAt = $now;
        $this->updatedAt = $now;

        $this->users = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    abstract public function getId(): ?int;

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
     * @param UserInterface $user
     * @return OperatorInterface
     */
    public function addUser(UserInterface $user): OperatorInterface
    {
        $user->setOperator( $this );
        $this->users[] = $user;

        return $this;
    }

    /**
     * @param UserInterface $user
     * @return OperatorInterface
     */
    public function removeUser(UserInterface $user): OperatorInterface
    {
        $this->users->removeElement($user);

        return $this;
    }

    /**
     * @param ArrayCollection $users
     * @return OperatorInterface
     */
    public function setUsers(ArrayCollection $users): OperatorInterface
    {
        if (null === $this->users) {
            $this->users = new ArrayCollection();
        }

        foreach($users as $user) {
            if (($user instanceof UserInterface) && (!$this->users->contains($users))) {
                $this->addUser($user);
            }
        }

        return $this;
    }

    /**
     * @return \Countable
     */
    public function getUsers(): \Countable
    {
        return $this->users;
    }

    /**
     * @param string $application
     * @return OperatorInterface
     */
    public function setApplication(string $application): OperatorInterface
    {
        $this->application = $application;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getApplication(): ?string
    {
        return $this->application;
    }

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

    /**
     * @param null|string $siret
     * @return OperatorInterface
     */
    public function setSiret(?string $siret): OperatorInterface
    {
        if (null !== $siret) {
            $this->siret = $siret;
        }

        return $this;
    }

    /**
     * @return null|string
     */
    public function getSiret(): ?string
    {
        return $this->siret;
    }

    /**
     * @param null|string $attestationCapacite
     * @return OperatorInterface
     */
    public function setAttestationCapacite(?string $attestationCapacite): OperatorInterface
    {
        if (null !== $attestationCapacite) {
            $this->attestationCapacite = $attestationCapacite;
        }

        return $this;
    }

    /**
     * @return null|string
     */
    public function getAttestationCapacite(): ?string
    {
        return $this->attestationCapacite;
    }
}
