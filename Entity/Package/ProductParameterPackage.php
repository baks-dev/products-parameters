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

namespace BaksDev\Products\Parameters\Entity\Package;


use BaksDev\Core\Entity\EntityEvent;
use BaksDev\DeliveryTransport\Type\ProductParameter\Weight\Kilogram\Kilogram;
use BaksDev\Products\Parameters\Entity\Event\ProductParameterEvent;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'product_parameter_package')]
class ProductParameterPackage extends EntityEvent
{
    /** Связь на событие */
    #[Assert\NotBlank]
    #[ORM\Id]
    #[ORM\OneToOne(targetEntity: ProductParameterEvent::class, inversedBy: 'package')]
    #[ORM\JoinColumn(name: 'event', referencedColumnName: 'id')]
    private ProductParameterEvent $event;

    /**
     * Вес, кг
     */
    #[Assert\NotBlank]
    #[ORM\Column(type: Kilogram::TYPE)]
    private Kilogram $weight;

    /**
     * Длина (Глубина), см
     */
    #[Assert\NotBlank]
    #[Assert\Range(min: 1, max: 32767)]
    #[ORM\Column(type: Types::SMALLINT)]
    private int $length;

    /**
     * Ширина, см
     */
    #[Assert\NotBlank]
    #[Assert\Range(min: 1, max: 32767)]
    #[ORM\Column(type: Types::SMALLINT)]
    private int $width;

    /**
     * Высота, см
     */
    #[Assert\NotBlank]
    #[Assert\Range(min: 1, max: 32767)]
    #[ORM\Column(type: Types::SMALLINT)]
    private int $height;

    /**
     * Объем, см3
     */
    #[Assert\NotBlank]
    #[Assert\Range(min: 1)]
    #[ORM\Column(type: Types::INTEGER)]
    private int $size;

    /**
     * Машиноместо
     */
    #[Assert\NotBlank]
    #[Assert\Range(min: 1)]
    #[ORM\Column(type: Types::INTEGER, options: ['default' => 1])]
    private int $package;


    public function __construct(ProductParameterEvent $event)
    {
        $this->event = $event;
    }

    public function __toString(): string
    {
        return (string) $this->event;
    }

    public function getDto($dto): mixed
    {
        if($dto instanceof ProductParameterPackageInterface)
        {
            return parent::getDto($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }


    public function setEntity($dto): mixed
    {
        if($dto instanceof ProductParameterPackageInterface)
        {
            /** Объем, см3 */
            $this->size = $dto->getWidth() * $dto->getLength() * $dto->getHeight();

            return parent::setEntity($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

}