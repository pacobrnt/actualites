<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SearchType; // Ajout de ce type pour le champ de recherche
use Symfony\Component\Form\Extension\Core\Type\SubmitType; // Ajout de ce type pour le bouton
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchFormType extends AbstractType
{
    /**
     * Définit la structure des champs du formulaire de recherche.
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // 1. Champ de saisie pour le mot-clé
            ->add('query', SearchType::class, [
                // 'query' sera le nom du paramètre dans l'URL (ex: ?query=mot-cle)
                'label' => false, // On n'affiche pas de label formel
                'attr' => [
                    'placeholder' => 'Rechercher par titre...',
                ],
                'required' => false, // Le champ n'est pas obligatoire
            ])

            // 2. Bouton de soumission
            ->add('submit', SubmitType::class, [
                'label' => 'Rechercher',
                'attr' => [
                    'class' => 'btn btn-primary', // (Optionnel) Ajout d'une classe pour le style
                ]
            ])
        ;
    }

    /**
     * Configure les options du formulaire.
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // La méthode 'GET' est indispensable pour un formulaire de recherche.
            // Cela met les paramètres dans l'URL (ex: /articles?query=...) et permet de partager les liens.
            'method' => 'GET',

            // On désactive la protection CSRF, car ce formulaire ne modifie pas les données en base.
            'csrf_protection' => false,

            // Le formulaire n'est lié à aucune Entité (data_class), ce qui est normal pour une recherche.
        ]);
    }
}
