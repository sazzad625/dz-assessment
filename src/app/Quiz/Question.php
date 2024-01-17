<?php


namespace App\Quiz;

use App\Models\QuestionType;
use App\Quiz\exceptions\UnsupportedTypeException;

/**
 * @author ilyas
 * Class Question
 * @package App\Quiz
 */
abstract class Question
{
    /**
     * This method returns array representation of current class excluding correct answers.<br/>
     * This method is safe when displaying question to student
     * @return array
     */
    public abstract function serializeExcludeAnswers();

    /**
     * This method returns array representation of current class.
     * @return array
     */
    public abstract function serialize();

    /**
     * returns type of the question
     * @return string
     */
    public abstract function getType();

    /**
     * Checks whether this question is right or wrong according to selected answers
     * @return boolean
     */
    public abstract function isCorrect();

    /**
     * This method updates the answer given so isCorrect method could perform check operations
     * @param $answer the answers given by student
     * @return mixed
     */
    public abstract function setAnswer($answer);

    /**
     * This method accepts the array of question
     * @param $questionArray
     * @return Question the class object depends on the type of question passes
     * @throws UnsupportedTypeException if the type does not matched with predefined types
     */
    public static function of($questionArray)
    {
        //if given array is empty or does not contains type key then through error
        if (empty($questionArray) || empty($questionArray['type'])) {
            throw new UnsupportedTypeException("No type exists in provided question");
        }

        switch ($questionArray['type']) {
            case QuestionType::TYPE_MULTI_CHOICE :
            {
                return new MultiChoiceQuestion($questionArray);
            }
            //more cases could be added when new question types are introduced
            default :
            {
                // in case of given type not matched throw exception
                throw new UnsupportedTypeException();
            }
        }
    }
}
