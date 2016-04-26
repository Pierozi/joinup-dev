<?php

namespace Drupal\joinup\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Checks if an entity field has a unique value (unique within it's bundle).
 *
 * This is specifically useful for base field, as you can limit the constraint
 * to certain bundles.
 *
 * @Constraint(
 *   id = "UniqueFieldInBundle",
 *   label = @Translation("Unique field within a bundle constraint", context = "Validation"),
 *   type = "string",
 * )
 */
class UniqueFieldInBundleConstraint extends Constraint {

  public $message = 'Content with @field_name %value already exists.';

  /**
   * {@inheritdoc}
   */
  public function validatedBy() {
    return '\Drupal\joinup\Plugin\Validation\Constraint\UniqueFieldValueInBundleValidator';
  }

  /**
   * The bundles for which this constraint applies.
   *
   * @var string
   */
  public $bundles;

  /**
   * {@inheritdoc}
   */
  public function getDefaultOption() {
    return 'bundles';
  }

  /**
   * {@inheritdoc}
   */
  public function getRequiredOptions() {
    return array('bundles');
  }

}
