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

namespace BaksDev\Products\Parameters\Entity\Invariable;

use BaksDev\Core\Entity\EntityReadonly;
use BaksDev\Products\Parameters\Entity\Event\ProductParameterEvent;
use BaksDev\Products\Parameters\Type\Id\ProductParameterUid;
use BaksDev\Products\Product\Type\Id\ProductUid;
use BaksDev\Products\Product\Type\Offers\ConstId\ProductOfferConst;
use BaksDev\Products\Product\Type\Offers\Variation\ConstId\ProductVariationConst;
use BaksDev\Products\Product\Type\Offers\Variation\Modification\ConstId\ProductModificationConst;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraints as Assert;

/* ProductParameters */

#[ORM\Entity]
#[ORM\Table(name: 'product_parameters_invariable')]
class ProductParameterInvariable extends EntityReadonly
{
    /**
     * Идентификатор Main
     */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Id]
    #[ORM\Column(type: ProductParameterUid::TYPE)]
    private ProductParameterUid $main;

    /**
     * Идентификатор События
     */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\OneToOne(targetEntity: ProductParameterEvent::class, inversedBy: 'invariable')]
    #[ORM\JoinColumn(name: 'event', referencedColumnName: 'id')]
    private ProductParameterEvent $event;


    /** ID продукта */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Column(type: ProductUid::TYPE)]
    private ProductUid $product;

    /** Постоянный уникальный идентификатор ТП */
    #[ORM\Column(type: ProductOfferConst::TYPE, nullable: true)]
    private ?ProductOfferConst $offer;

    /** Постоянный уникальный идентификатор варианта */
    #[ORM\Column(type: ProductVariationConst::TYPE, nullable: true)]
    private ?ProductVariationConst $variation;

    /** Постоянный уникальный идентификатор модификации */
    #[ORM\Column(type: ProductModificationConst::TYPE, nullable: true)]
    private ?ProductModificationConst $modification;


    public function __construct(ProductParameterEvent $event)
    {
        $this->event = $event;
        $this->main = $event->getMain();
    }


    public function __toString(): string
    {
        return (string) $this->main;
    }

    public function getId(): ProductParameterUid
    {
        return $this->main;
    }

    public function setEvent(ProductParameterEvent $event): self
    {
        $this->event = $event;
        return $this;
    }


    public function getDto($dto): mixed
    {
        $dto = is_string($dto) && class_exists($dto) ? new $dto() : $dto;

        if($dto instanceof ProductParameterInvariableInterface)
        {

            /** Переводим вес граммы в килограммы   */
            return parent::getDto($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

    public function setEntity($dto): mixed
    {
        if($dto instanceof ProductParameterInvariableInterface || $dto instanceof self)
        {
            return parent::setEntity($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }
}
