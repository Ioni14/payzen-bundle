<?php

namespace Ioni\PayzenBundle\Form\Type;

use Ioni\PayzenBundle\Model\Transaction;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransactionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();
            /** @var Transaction $transaction */
            $transaction = $event->getData();

            $form
                ->add('vads_currency', HiddenType::class, [
                    'data' => $transaction->getCurrency(),
                ])
                ->add('vads_amount', HiddenType::class, [
                    'data' => $transaction->getAmount(),
                ])
            ;
        });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Transaction::class,
        ]);
    }
}
