<?php

namespace MD\SocomBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use FOS\UserBundle\Model\UserInterface as FosUserInterface;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Accessor;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Operator
 *
 * @ORM\MappedSuperclass()
 * @UniqueEntity("email")
 */
abstract class AbstractUser extends BaseUser implements FosUserInterface, UserInterface
{
    protected $roles = array();

    /**
     * @Type("string")
     * @ORM\Column(type="string", nullable=true)
     * @Accessor(getter="getPhone",setter="setPhone")
     */
    protected $phone;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Accessor(getter="getCreatedAt",setter="setCreatedAt")
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Accessor(getter="getUpdatedAt",setter="setUpdatedAt")
     */
    protected $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="MD\SocomBundle\Model\OperatorInterface", inversedBy="users")
     * @ORM\JoinColumn(nullable=false)
     * @Accessor(getter="getOperator",setter="setOperator")
     */
    protected $operator;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     * @Type("boolean")
     * @Accessor(getter="getAdmin",setter="setAdmin")
     */
    protected $admin = false;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     * @Type("boolean")
     * @Accessor(getter="getAccountant",setter="setAccountant")
     */
    protected $accountant = false;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Type("string")
     * @Accessor(getter="getType",setter="setType")
     */
    protected $type = self::ADMINISTRATIF;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @Accessor(getter="getLastName",setter="setLastName")
     * @Type("string")
     */
    protected $lastName;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @Accessor(getter="getFirstName",setter="setFirstName")
     * @Type("string")
     */
    protected $firstName;

    /**
     * @Accessor(getter="getPlainPassword",setter="setPlainPassword")
     * @Type("string")
     */
    protected $plainPassword;

    /**
     * @Accessor(getter="getEmail",setter="setEmail")
     * @Type("string")
     */
    protected $email;

    /**
     * AbstractUser constructor.
     * @param OperatorInterface|null $operator
     * @throws \Exception
     */
    public function __construct(OperatorInterface $operator = null)
    {
        $now = new \DateTime();
        $this->createdAt = $now;
        $this->updatedAt = $now;

        parent::__construct();
    }

    public function setEmail($email)
    {
        parent::setEmail(strtolower($email));
        $this->setUsername($this->getEmail());

        return $this;
    }

    /**
     * @param null|string $phone
     * @return UserInterface
     */
    public function setPhone(?string $phone): UserInterface
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
     * @return UserInterface
     */
    public function setCreatedAt(\DateTime $createdAt): UserInterface
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
     * @return UserInterface
     */
    public function setUpdatedAt(\DateTime $updatedAt): UserInterface
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

    public function setOperator(OperatorInterface $operator): UserInterface
    {
        $this->operator = $operator;

        return $this;
    }

    /**
     * @return OperatorInterface
     */
    public function getOperator(): OperatorInterface
    {
        return $this->operator;
    }

    /**
     * @param bool $admin
     * @return UserInterface
     */
    public function setAdmin(bool $admin): UserInterface
    {
        $this->admin = $admin;

        if ($this->getType() == self::ADMINISTRATIF) {
            $this->admin = true;
        }

        if ($this->getType() == self::COMPTABILITE) {
            $this->accountant = true;
        }

        if ($this->admin === true) {
            $this->addRole('ROLE_ADMIN');
        } else {
            $this->removeRole('ROLE_ADMIN');
        }

        if ($this->accountant === true) {
            $this->addRole('ROLE_COMPTA');
        } else {
            $this->removeRole('ROLE_COMPTA');
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function getAdmin(): bool
    {
        if (null === $this->admin) {
            return false;
        }

        return $this->admin;
    }

    /**
     * @param $type
     * @return $this
     */
    public function setType(string $type): UserInterface
    {
        if (($type != self::MAGASINIER) && ($type != self::ADMINISTRATIF) && ($type != self::COMPTABILITE)) {
            $this->type = self::TECHNICIEN;
        }

        $this->type = $type;

        if (($type == self::ADMINISTRATIF) || ($type == self::COMPTABILITE)) {
            $this->setAdmin(true);
        }

        if ($type == self::COMPTABILITE) {
            $this->setAdmin(true);
        }

        return $this;
    }

    /**
     * @return null|string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string $lastName
     * @return UserInterface
     */
    public function setLastName(string $lastName): UserInterface
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param string $firstName
     * @return UserInterface
     */
    public function setFirstName(string $firstName): UserInterface
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param bool $
     *
     *
     *
     * ntant
     * @return UserInterface
     */
    public function setAccountant(bool $accountant): UserInterface
    {
        $this->accountant = $accountant;

        if ($accountant === true) {
            $this->addRole('ROLE_COMPTA');
            $this->addRole('ROLE_ADMIN');
        } else {
            $this->removeRole('ROLE_COMPTA');
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function getAccountant(): bool
    {
        return $this->accountant;
    }
}
