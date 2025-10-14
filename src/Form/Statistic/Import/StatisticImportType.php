<?php

namespace App\Form\Statistic\Import;

use App\Domain\Statistic\Import\Model\ImportTypeModel;
use App\Entity\Game;
use App\Entity\Team;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfonycasts\DynamicForms\DependentField;
use Symfonycasts\DynamicForms\DynamicFormBuilder;

class StatisticImportType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ImportTypeModel::class,
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder = new DynamicFormBuilder($builder);
        $builder->add(child: 'team', type: EntityType::class, options: [
            'class' => Team::class,
            'choice_label' => 'name',
            'label' => 'Team',
            'required' => true,
            'placeholder' => 'Wähle ein Team aus',
        ]);

        $builder->addDependent('game', 'team', function (DependentField $field, ?Team $team) {
            /* if (!$team) { */
            /*     return; */
            /* } */

            $field
                ->add(type: EntityType::class, options: [
                    'class' => Game::class,
                    'label' => 'Spiel',
                    'required' => true,
                    'placeholder' => 'Wähle ein Spiel',
                    'attr' => null === $team ? ['disabled' => true] : [],
                    'query_builder' => function (EntityRepository $er) use ($team): QueryBuilder {
                        if (!$team) {
                            return $er->createQueryBuilder('game')
                                ->where('0=1');
                        }

                        return $er->createQueryBuilder('game')
                            ->innerJoin('game.teamOne', 'team1')
                            ->innerJoin('game.teamTwo', 'team2')
                            ->where('team1.id = :team')
                            ->orWhere('team2.id = :team')
                            ->setParameter('team', $team->getId())
                            ->orderBy('game.date');
                    },
                ]);
        });

        $builder->addDependent('videoId', 'game', function (DependentField $field, ?Game $game) {
            /* if (!$game) { */
            /*     return; */
            /* } */
            $field
                ->add(type: TextType::class, options: [
                    'label' => 'Video-ID',
                    'help' => 'Die Video-ID ist der letzte Teil der URL, auf der Seite wo das Video angezeigt wird.',
                    'required' => true,
                    'attr' => null === $game ? ['disabled' => true] : [],
                ]);
        });

        $builder->addDependent('submit', 'videoId', function (DependentField $field, ?string $videoId) {
            $field
                ->add(type: SubmitType::class, options: [
                    'attr' => null === $videoId ? ['disabled' => true] : [],
                ]
                );
        });
    }
}
