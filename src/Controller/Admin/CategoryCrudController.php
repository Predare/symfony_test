<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Category::class;
    }
    
    public function configureFields(string $pageName): iterable
    {

        yield IdField::new('id')->onlyOnIndex();
        yield TextField::new('title');
        yield AssociationField::new('books')
            ->setFormTypeOption('choice_label', 'title');
        yield AssociationField::new('subCategories')
            ->setFormTypeOption('choice_label', 'title');
    }
}
