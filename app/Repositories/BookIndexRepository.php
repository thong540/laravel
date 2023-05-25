<?php

namespace App\Repositories;

use App\Models\BookIndex;
use App\Models\QuestionSet;

class BookIndexRepository extends EloquentRepository
{
    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return BookIndex::class;
    }

    public function getBookIndex($bookId, $type) {
        $query = $this->_model->where('book_id', $bookId)
            ->where('type', $type)
            ->where('parent_id', 0)
            ->with('children')
            ->with('questions');

        return $query->get();
    }

    public function getBookIndexBySubjectId($subjectId, $gradeId, $type) {
        return $this->_model->where('subject_id', $subjectId)
            ->where('type', $type)
            ->where('grade_id', $gradeId)
            ->where('parent_id', 0)
            ->with('children')
            ->with(['children' => function ($query) {
                $query->orderBy('sort', 'asc');
            }])
            ->with('questions')
            ->orderBy('sort', 'asc')
            ->get();
    }

    public function getListExam($gradeId, $subjectId, $type, $userHoc10 = false) {
        $query = $this->_model
            ->where('grade_id', $gradeId)
            ->where('subject_id', $subjectId)
            ->where('type', $type)
            ->where('parent_id', 0)
            ->orderBy('sort', 'asc');
        if($userHoc10) {
            $query = $query->with(['children' => function($query) {
                $query->orderBy('sort', 'asc');
            }])->with(['questions' => function($query) {
                return $query->orderBy('time_publish', 'desc');
            }]);
        } else {
            $query = $query->with('childrenHasPublishQuestion')
                            ->with(['questions' => function($query) {
                                return $query->where('status', QuestionSet::PUBLISH)->orderBy('order', 'asc')->orderBy('time_publish', 'desc');
                            }]);
        }

        return $query->get();
    }
}
