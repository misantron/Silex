<?php

namespace Silex\Tests\Provider\FormServiceProviderTest;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DummyFormTypeExtension extends AbstractTypeExtension
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined(['image_path']);
    }

    public static function getExtendedTypes(): iterable
    {
        return [FileType::class];
    }
}
