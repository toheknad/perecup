<?php

namespace App\Module\AddressNormalizer\Service;

class DefaultDriver implements StreetNormalizerInterface
{
    public const TYPES_NAME_STREET = [
        [
            'улица', //ФИАС Спартака Улица - улица ставится после названия
            'ул.',
        ],
        [
            'проспект', // ФИАС Энергетиков проспект - проспект ставится после названия
            'просп.',
            'пр-кт',
            'пр-т',
        ],
        [
            'заезд' // ФИАС Майский Заезд
        ]
    ];


    /**
     * Возвращает значение по ФИАС
     * @param string $address
     * @return array
     */
    public function normalize(string $address): array
    {
        [$street, $house] = $this->getDataFromAddress($address);

        return [
            'street' => $street,
            'house' => $house
        ];
    }

    private function getDataFromAddress(string $address): array
    {
        $pos = null;
        $addressArray = mb_str(",",$address);
//        print_r($addressArray);
//        echo mb_substr($address, 3);
//        echo "---";
        foreach (self::TYPES_NAME_STREET as $type) {
            print_r($type);
            foreach ($type as $abbreviation) {
                $pos = mb_stripos($address, $abbreviation);
                if ($pos !== false) {
                    $foundPosition = $pos;
                }
            }
        }
//        print_r('FOUND:' . $foundPosition);
        if ($foundPosition) {
            $foundPosition--;
            $startPositionFound = false;
            $startPosition = null;
            while (!$startPositionFound) {
                echo $address[$foundPosition];
                if ($address[$foundPosition] === ',') {
                    $startPositionFound = true;
                    $startPosition = $foundPosition;
                }
                $foundPosition--;
            }
            $endPositionFound = false;
            $endPosition = null;
            while (!$endPositionFound) {
                if ($address[$foundPosition] === ',') {
                    $endPositionFound = true;
                    $endPosition = $foundPosition;
                }
                $foundPosition++;
            }
            $street = mb_substr($address, $startPosition+1);
            $house = mb_substr($address, $endPosition+1);
            return [
                $street,
                $house
            ];
        }
        return [];
    }
}