<?php

declare (strict_types=1);
namespace RectorPrefix20211123;

use Rector\CodeQuality\Rector\Return_\SimplifyUselessVariableRector;
use Rector\DeadCode\Rector\Array_\RemoveDuplicatedArrayKeyRector;
use Rector\DeadCode\Rector\Assign\RemoveAssignOfVoidReturnFunctionRector;
use Rector\DeadCode\Rector\Assign\RemoveDoubleAssignRector;
use Rector\DeadCode\Rector\Assign\RemoveUnusedVariableAssignRector;
use Rector\DeadCode\Rector\BinaryOp\RemoveDuplicatedInstanceOfRector;
use Rector\DeadCode\Rector\BooleanAnd\RemoveAndTrueRector;
use Rector\DeadCode\Rector\Cast\RecastingRemovalRector;
use Rector\DeadCode\Rector\ClassConst\RemoveUnusedPrivateClassConstantRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveDeadConstructorRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveDelegatingParentCallRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveEmptyClassMethodRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveLastReturnRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUnusedConstructorParamRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUnusedPrivateMethodParameterRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUnusedPrivateMethodRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUnusedPromotedPropertyRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUselessParamTagRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUselessReturnTagRector;
use Rector\DeadCode\Rector\Concat\RemoveConcatAutocastRector;
use Rector\DeadCode\Rector\Expression\RemoveDeadStmtRector;
use Rector\DeadCode\Rector\Expression\SimplifyMirrorAssignRector;
use Rector\DeadCode\Rector\For_\RemoveDeadIfForeachForRector;
use Rector\DeadCode\Rector\For_\RemoveDeadLoopRector;
use Rector\DeadCode\Rector\Foreach_\RemoveUnusedForeachKeyRector;
use Rector\DeadCode\Rector\FunctionLike\RemoveCodeAfterReturnRector;
use Rector\DeadCode\Rector\FunctionLike\RemoveDeadReturnRector;
use Rector\DeadCode\Rector\FunctionLike\RemoveDuplicatedIfReturnRector;
use Rector\DeadCode\Rector\FunctionLike\RemoveOverriddenValuesRector;
use Rector\DeadCode\Rector\If_\RemoveDeadInstanceOfRector;
use Rector\DeadCode\Rector\If_\RemoveUnusedNonEmptyArrayBeforeForeachRector;
use Rector\DeadCode\Rector\If_\SimplifyIfElseWithSameContentRector;
use Rector\DeadCode\Rector\If_\UnwrapFutureCompatibleIfFunctionExistsRector;
use Rector\DeadCode\Rector\If_\UnwrapFutureCompatibleIfPhpVersionRector;
use Rector\DeadCode\Rector\MethodCall\RemoveEmptyMethodCallRector;
use Rector\DeadCode\Rector\Node\RemoveNonExistingVarAnnotationRector;
use Rector\DeadCode\Rector\Property\RemoveUnusedPrivatePropertyRector;
use Rector\DeadCode\Rector\PropertyProperty\RemoveNullPropertyInitializationRector;
use Rector\DeadCode\Rector\Return_\RemoveDeadConditionAboveReturnRector;
use Rector\DeadCode\Rector\StaticCall\RemoveParentCallWithoutParentRector;
use Rector\DeadCode\Rector\Stmt\RemoveUnreachableStatementRector;
use Rector\DeadCode\Rector\Switch_\RemoveDuplicatedCaseInSwitchRector;
use Rector\DeadCode\Rector\Ternary\TernaryToBooleanOrFalseToBooleanAndRector;
use Rector\DeadCode\Rector\TryCatch\RemoveDeadTryCatchRector;
use Rector\PHPUnit\Rector\ClassMethod\RemoveEmptyTestMethodRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
return static function (\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->set(\Rector\DeadCode\Rector\If_\UnwrapFutureCompatibleIfFunctionExistsRector::class);
    $services->set(\Rector\DeadCode\Rector\If_\UnwrapFutureCompatibleIfPhpVersionRector::class);
    $services->set(\Rector\DeadCode\Rector\Cast\RecastingRemovalRector::class);
    $services->set(\Rector\DeadCode\Rector\Expression\RemoveDeadStmtRector::class);
    $services->set(\Rector\DeadCode\Rector\Array_\RemoveDuplicatedArrayKeyRector::class);
    $services->set(\Rector\DeadCode\Rector\Foreach_\RemoveUnusedForeachKeyRector::class);
    $services->set(\Rector\DeadCode\Rector\StaticCall\RemoveParentCallWithoutParentRector::class);
    $services->set(\Rector\DeadCode\Rector\ClassMethod\RemoveEmptyClassMethodRector::class);
    $services->set(\Rector\DeadCode\Rector\Assign\RemoveDoubleAssignRector::class);
    $services->set(\Rector\DeadCode\Rector\Expression\SimplifyMirrorAssignRector::class);
    $services->set(\Rector\DeadCode\Rector\FunctionLike\RemoveOverriddenValuesRector::class);
    $services->set(\Rector\DeadCode\Rector\Property\RemoveUnusedPrivatePropertyRector::class);
    $services->set(\Rector\DeadCode\Rector\ClassConst\RemoveUnusedPrivateClassConstantRector::class);
    $services->set(\Rector\DeadCode\Rector\ClassMethod\RemoveUnusedPrivateMethodRector::class);
    $services->set(\Rector\DeadCode\Rector\FunctionLike\RemoveCodeAfterReturnRector::class);
    $services->set(\Rector\DeadCode\Rector\ClassMethod\RemoveDeadConstructorRector::class);
    $services->set(\Rector\DeadCode\Rector\FunctionLike\RemoveDeadReturnRector::class);
    $services->set(\Rector\DeadCode\Rector\For_\RemoveDeadIfForeachForRector::class);
    $services->set(\Rector\DeadCode\Rector\BooleanAnd\RemoveAndTrueRector::class);
    $services->set(\Rector\DeadCode\Rector\Concat\RemoveConcatAutocastRector::class);
    $services->set(\Rector\CodeQuality\Rector\Return_\SimplifyUselessVariableRector::class);
    $services->set(\Rector\DeadCode\Rector\ClassMethod\RemoveDelegatingParentCallRector::class);
    $services->set(\Rector\DeadCode\Rector\BinaryOp\RemoveDuplicatedInstanceOfRector::class);
    $services->set(\Rector\DeadCode\Rector\Switch_\RemoveDuplicatedCaseInSwitchRector::class);
    $services->set(\Rector\DeadCode\Rector\PropertyProperty\RemoveNullPropertyInitializationRector::class);
    $services->set(\Rector\DeadCode\Rector\Stmt\RemoveUnreachableStatementRector::class);
    $services->set(\Rector\DeadCode\Rector\If_\SimplifyIfElseWithSameContentRector::class);
    $services->set(\Rector\DeadCode\Rector\Ternary\TernaryToBooleanOrFalseToBooleanAndRector::class);
    $services->set(\Rector\PHPUnit\Rector\ClassMethod\RemoveEmptyTestMethodRector::class);
    $services->set(\Rector\DeadCode\Rector\TryCatch\RemoveDeadTryCatchRector::class);
    $services->set(\Rector\DeadCode\Rector\Assign\RemoveUnusedVariableAssignRector::class);
    $services->set(\Rector\DeadCode\Rector\FunctionLike\RemoveDuplicatedIfReturnRector::class);
    $services->set(\Rector\DeadCode\Rector\If_\RemoveUnusedNonEmptyArrayBeforeForeachRector::class);
    $services->set(\Rector\DeadCode\Rector\Assign\RemoveAssignOfVoidReturnFunctionRector::class);
    $services->set(\Rector\DeadCode\Rector\MethodCall\RemoveEmptyMethodCallRector::class);
    $services->set(\Rector\DeadCode\Rector\Return_\RemoveDeadConditionAboveReturnRector::class);
    $services->set(\Rector\DeadCode\Rector\ClassMethod\RemoveUnusedConstructorParamRector::class);
    $services->set(\Rector\DeadCode\Rector\If_\RemoveDeadInstanceOfRector::class);
    $services->set(\Rector\DeadCode\Rector\For_\RemoveDeadLoopRector::class);
    $services->set(\Rector\DeadCode\Rector\ClassMethod\RemoveUnusedPrivateMethodParameterRector::class);
    // docblock
    $services->set(\Rector\DeadCode\Rector\ClassMethod\RemoveUselessParamTagRector::class);
    $services->set(\Rector\DeadCode\Rector\ClassMethod\RemoveUselessReturnTagRector::class);
    $services->set(\Rector\DeadCode\Rector\Node\RemoveNonExistingVarAnnotationRector::class);
    $services->set(\Rector\DeadCode\Rector\ClassMethod\RemoveUnusedPromotedPropertyRector::class);
    $services->set(\Rector\DeadCode\Rector\ClassMethod\RemoveLastReturnRector::class);
};