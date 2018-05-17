<?php

namespace MD\SocomBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;

class OTagCommand
{
    /**
     * @Assert\Length(max=25)
     */
    protected $clientReference = null;

    /**
     * @Assert\Range(min = "2")
     */
    protected $quantity = 2;

    protected $operator;

    public function __construct(OperatorInterface $operator)
    {
        $this->setOperator($operator);
    }

    /**
     * @param string $clientReference
     * @return OTagCommand
     */
    public function setClientReference(string $clientReference): OTagCommand
    {
        $this->clientReference = $clientReference;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getClientReference(): ?string
    {
        return $this->clientReference;
    }

    /**
     * @param int $quantity
     * @return OTagCommand
     */
    public function setQuantity(int $quantity): OTagCommand
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    /**
     * @param OperatorInterface $operator
     * @return OTagCommand
     */
    public function setOperator(OperatorInterface $operator): OTagCommand
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
}
