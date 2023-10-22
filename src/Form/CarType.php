<?php

namespace App\Form;

use App\Entity\Car;
use App\Form\ImageType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class CarType extends AbstractType
{
    /**
     * Fusion récursive de tableau - structure
     *
     * @param string $label
     * @param string $placeholder
     * @param array $options
     * @return array
     */
    private function getConfiguration(string $label, string $placeholder, array $options=[]):array
    {
        return array_merge_recursive([
                'label'=> $label,
                'attr'=> [
                    'placeholder'=> $placeholder
                ],
            ], $options
        );
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('model', TextType::class, $this->getConfiguration('Modèle', 'Exemple : Giulia'))
            ->add('brand', TextType::class, $this->getConfiguration('Marque', 'Exemple : Alfa Romeo'))
            ->add('slug', TextType::class, $this->getConfiguration('Slug', 'Adresse web (automatique)',[
                'required' => false
            ]))
            ->add('coverImage', UrlType::class, $this->getConfiguration('Image de couverture', 'Veuillez fournir un URL valide'))
            ->add('km', IntegerType::class, $this->getConfiguration('Kilomètrage', 'Indiquez le nombre de kilomètres du véhicule...'))
            ->add('price', MoneyType::class, $this->getConfiguration('Prix', 'Indiquez le prix du véhicule...'))
            ->add('owners', IntegerType::class, $this->getConfiguration('Nombre de propriétaires', 'Indiquez le nombre de propriétaires du véhicule...'))
            ->add('cylinder', NumberType::class, $this->getConfiguration('Cylindrée', 'Indiquez la cylindrée du véhicule...'))
            ->add('power', IntegerType::class, $this->getConfiguration('Puissance', 'Indiquez la puissance en chevaux du véhicule...'))
            ->add('carburant', ChoiceType::class, [
                'choices'=>[
                    'Essence'=>'essence',
                    'Diesel'=>'diesel',
                    'Electrique'=>'electrique',
                    'Hybride'=>'hybride',
                    'LPG'=>'lpg',
                    'CNG'=>'cng',
                ]])
            ->add('year', IntegerType::class, $this->getConfiguration('Année de mise en circulation', 'Indiquez l\'année de mise en circulation du véhicule...'))
            ->add('transmission', ChoiceType::class, [
                'choices'  => [
                    'Automatique' => 'Automatique',
                    'Manuel' => 'Manuel',
                ]])
            ->add('content', TextareaType::class, $this->getConfiguration('Description', 'Décrivez le véhicule'))
            ->add('options', TextareaType::class, $this->getConfiguration('Options', 'Listez les options du véhicule'))
            ->add('images', CollectionType::class,[
                'entry_type'=> ImageType::class,
                'allow_add'=> true,
                'allow_delete'=>true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Car::class,
        ]);
    }
}
