<?php

namespace App\Controller\Admin;

use App\Entity\FeedbackForm;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class FeedbackFormCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return FeedbackForm::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
