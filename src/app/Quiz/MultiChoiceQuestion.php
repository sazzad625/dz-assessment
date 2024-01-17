<?php


namespace App\Quiz;


use App\Helpers\HtmlContentHelper;
use App\Models\QuestionType;
use http\Exception\InvalidArgumentException;

/**
 * This is a sample json for Multi Choice
 * {
 *  text: 'Choose correct answer from below options',
 *  options: [
 *      {text: 'option 1', isCorrect: true},
 *      {text: 'option 2', isCorrect: false}
 *  ]
 * }
 *
 * ---------------------------------------------------------
 *
 * This is a sample json when question will be returning with answer chosen
 * [0,1]
 */
class MultiChoiceQuestion extends Question
{
    private $text;
    private $options = [];
    //given by student
    private $answers = [];

    const FIELD_TEXT = 'text';
    const FIELD_OPTIONS = 'options';
    const FIELD_IS_CORRECT = 'isCorrect';
    const FIELD_ANSWERS = 'answers';
    const FIELD_TYPE = 'type';


    public function __construct($questionArray)
    {
        $this->text = $questionArray[self::FIELD_TEXT];
        $this->options = $questionArray[self::FIELD_OPTIONS];
    }

    /**
     * This method returns array representation of current class
     * @return array
     */
    public function serialize()
    {
        return [
            self::FIELD_TEXT => HtmlContentHelper::getHtmlContent($this->text), // changing content html img/videos src so it could be viewable
            self::FIELD_OPTIONS => $this->options,
            self::FIELD_ANSWERS => !empty($this->answers) ? $this->answers : [],
            self::FIELD_TYPE => $this->getType()
        ];
    }

    /**
     * Returns type of the question
     * @return string
     */
    public function getType()
    {
        return QuestionType::TYPE_MULTI_CHOICE;
    }

    /**
     * Checks whether this question is right or wrong according to selected answers
     * @return boolean
     */
    public function isCorrect()
    {
        //if no answer is set then its incorrect
        if (empty($this->answers)) {
            return false;
        }

        //to store correct options indexes
        $correctIndexes = [];

        foreach ($this->options as $index => $option) {
            //if current option is correct then store is index in temp variable
            if ($option[self::FIELD_IS_CORRECT] == true) {
                $correctIndexes[] = $index;
            }
        }

        //sorting both arrays so comparison would be easy
        sort($correctIndexes);
        sort($this->answers);

        return $correctIndexes === $this->answers;
    }

    /**
     * This method returns array representation of current class excluding correct answers.<br/>
     * This method is safe when displaying question to student
     * @return array
     */
    public function serializeExcludeAnswers()
    {
        $tempOptions = [];
        //iterate over options and keep it in temp variable excluding answers
        foreach ($this->options as $option) {
            $tempOptions[] = [
                self::FIELD_TEXT => $option[self::FIELD_TEXT]
            ];
        }

        //return final question
        return [
            self::FIELD_TEXT => HtmlContentHelper::getHtmlContent($this->text), // changing content html img/videos src so it could be viewable
            self::FIELD_OPTIONS => $tempOptions,
            self::FIELD_ANSWERS => !empty($this->answers) ? $this->answers : [],
            self::FIELD_TYPE => $this->getType()
        ];
    }

    /**
     * This method updates the answer given so isCorrect method could perform check operations
     * @param $answer the answers given by student
     * @return mixed
     */
    public function setAnswer($answer)
    {
        $this->answers = $answer;
    }
}
