<?php

namespace MD\SocomBundle\Model;

interface OperatorInterface
{
    /**
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * @param string $company
     * @return OperatorInterface
     */
    public function setCompany(string $company): OperatorInterface;

    /**
     * @return null|string
     */
    public function getCompany(): ?string;

    /**
     * @param string $application
     * @return OperatorInterface
     */
    public function setApplication(string $application): OperatorInterface;

    /**
     * @return null|string
     */
    public function getApplication(): ?string;

    /**
     * @param string $zipCode
     * @return OperatorInterface
     */
    public function setZipCode(string $zipCode): OperatorInterface;

    /**
     * @return null|string
     */
    public function getZipCode(): ?string;

    /**
     * @param string $address
     * @return OperatorInterface
     */
    public function setAddress(string $address): OperatorInterface;

    /**
     * @return null|string
     */
    public function getAddress(): ?string;

    /**
     * @param string $city
     * @return OperatorInterface
     */
    public function setCity(string $city): OperatorInterface;

    /**
     * @return null|string
     */
    public function getCity(): ?string;

    /**
     * @param string $email
     * @return OperatorInterface
     */
    public function setEmail(string $email): OperatorInterface;

    /**
     * @return null|string
     */
    public function getEmail(): ?string;

    /**
     * @param string $contact
     * @return OperatorInterface
     */
    public function setContact(string $contact): OperatorInterface;

    /**
     * @return null|string
     */
    public function getContact(): ?string;

    /**
     * @param string $phone
     * @return OperatorInterface
     */
    public function setPhone(string $phone): OperatorInterface;

    /**
     * @return null|string
     */
    public function getPhone(): ?string;

    /**
     * @param \DateTime $createdAt
     * @return OperatorInterface
     */
    public function setCreatedAt(\DateTime $createdAt): OperatorInterface;

    /**
     * @return \DateTime|null
     */
    public function getCreatedAt(): ?\DateTime;

    /**
     * @param \DateTime $updatedAt
     * @return OperatorInterface
     */
    public function setUpdatedAt(\DateTime $updatedAt): OperatorInterface;

    /**
     * @return \DateTime|null
     */
    public function getUpdatedAt(): ?\DateTime;

    /**
     * @return \Countable
     */
    public function getUsers(): \Countable;

    /**
     * @param UserInterface $user
     * @return OperatorInterface
     */
    public function addUser(UserInterface $user): OperatorInterface;

    /**
     * @param UserInterface $user
     * @return OperatorInterface
     */
    public function removeUser(UserInterface $user): OperatorInterface;

    /**
     * @param null|string $siret
     * @return OperatorInterface
     */
    public function setSiret(?string $siret): OperatorInterface;

    /**
     * @return null|string
     */
    public function getSiret(): ?string;

    /**
     * @param null|string $attestationCapacite
     * @return OperatorInterface
     */
    public function setAttestationCapacite(?string $attestationCapacite): OperatorInterface;

    /**
     * @return null|string
     */
    public function getAttestationCapacite(): ?string;
}
