<?php
/*
 *  Copyright 2024.  Baks.dev <admin@baks.dev>
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is furnished
 *  to do so, subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 */

declare(strict_types=1);

namespace BaksDev\Products\Parameters\Entity\Event;

use BaksDev\Core\Entity\EntityEvent;
use BaksDev\Products\Parameters\Entity\Invariable\ProductParameterInvariable;
use BaksDev\Products\Parameters\Entity\Modify\ProductParameterModify;
use BaksDev\Products\Parameters\Entity\Package\ProductParameterPackage;
use BaksDev\Products\Parameters\Entity\ProductParameter;
use BaksDev\Products\Parameters\Entity\Property\ProductParameterProperty;
use BaksDev\Products\Parameters\Type\Event\ProductParameterEventUid;
use BaksDev\Products\Parameters\Type\Id\ProductParameterUid;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Collection;


/* ProductParameterEvent */

#[ORM\Entity]
#[ORM\Table(name: 'product_parameter_event')]
class ProductParameterEvent extends EntityEvent
{
    /**
     * Идентификатор События
     */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Id]
    #[ORM\Column(type: ProductParameterEventUid::TYPE)]
    private ProductParameterEventUid $id;

    /**
     * Идентификатор ProductParameter
     */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Column(type: ProductParameterUid::TYPE, nullable: false)]
    private ?ProductParameterUid $main = null;


    /** Неизменяемые параметры */
    #[ORM\OneToOne(targetEntity: ProductParameterInvariable::class, mappedBy: 'event', cascade: ['all'], fetch: 'EAGER')]
    private ProductParameterInvariable $invariable;

    /**
     * Свойства карточки
     */
    #[ORM\OneToMany(targetEntity: ProductParameterProperty::class, mappedBy: 'event', cascade: ['all'], fetch: 'EAGER')]
    private Collection $property;


    /** Параметры упаковки */
    #[ORM\OneToOne(targetEntity: ProductParameterPackage::class, mappedBy: 'event', cascade: ['all'], fetch: 'EAGER')]
    private ?ProductParameterPackage $package = null;

    /**
     * Модификатор
     */
    #[ORM\OneToOne(targetEntity: ProductParameterModify::class, mappedBy: 'event', cascade: ['all'], fetch: 'EAGER')]
    private ProductParameterModify $modify;


    public function __construct()
    {
        $this->id = new ProductParameterEventUid();
        $this->modify = new ProductParameterModify($this);

    }

    public function __clone()
    {
        $this->id = clone new ProductParameterEventUid();
    }

    public function __toString(): string
    {
        return (string) $this->id;
    }

    public function getId(): ProductParameterEventUid
    {
        return $this->id;
    }

    /**
     * Идентификатор ProductParameter
     */
    public function setMain(ProductParameterUid|ProductParameter $main): void
    {
        $this->main = $main instanceof ProductParameter ? $main->getId() : $main;
    }


    public function getMain(): ?ProductParameterUid
    {
        return $this->main;
    }

    public function getDto($dto): mixed
    {
        if($dto instanceof ProductParameterEventInterface)
        {
            return parent::getDto($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

    public function setEntity($dto): mixed
    {
        if($dto instanceof ProductParameterEventInterface)
        {
            return parent::setEntity($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }
}