
	
	function getResultSchema() {
		if(QuizData.type == 'scoreBased') {
			Schemas.resultSchema.minScore = origResultSchema.minScore;
		} else {
			delete Schemas.resultSchema.minScore;
		}
		return Schemas.resultSchema;
	}

	var origQuestionSchema = $.extend(true, {}, Schemas.questionSchema);
	var origResultSchema = $.extend(true, {}, Schemas.resultSchema);
	
	function getQuestionSchema() {
        var correctAnswerSchema = $.extend(true, {}, Schemas.questionSchema.choices.items.properties.correct);
        var favoursResultSchema = $.extend(true, {}, Schemas.questionSchema.choices.items.properties.favoursResult);

        //Deleting correct and favoursResult propertiesfirst
        var propertiesToDelete = ['correct', 'favoursResult'];
        _.each(propertiesToDelete, function(property) {
            if(Schemas.questionSchema.choices.items.properties.hasOwnProperty(property)) {
                delete Schemas.questionSchema.choices.items.properties[property];
            }
        })

        //Copy and delete the "correctAnswerExplanation" property
        var correctAnswerExplanationSchema = $.extend(true, {}, Schemas.questionSchema.correctAnswerExplanation);
        delete Schemas.questionSchema.correctAnswerExplanation;

        if(QuizData.type != 'random') {

		}
        if(QuizData.type == 'scoreBased') {
            Schemas.questionSchema.choices.items.properties.correct = correctAnswerSchema;
            Schemas.questionSchema.correctAnswerExplanation = correctAnswerExplanationSchema;
        }
        if(QuizData.type == 'prediction') {
			Schemas.questionSchema.choices.items.properties.favoursResult = favoursResultSchema;
		}
		return Schemas.questionSchema;
	}

	
	function setQuizData(newQuizData, excludeProperties) {
		excludeProperties = excludeProperties || [];
		for(var i in newQuizData) {
			if(excludeProperties.indexOf(i) < 0) {
				QuizData[i] = newQuizData[i];
			}
		}
		//debugger;
		return QuizData;
	}

	function getResultTextFromId(id) {
		var resultItem = _.findWhere(QuizData.results, {id: id});
		if(resultItem) {
			return resultItem.title;
		} else {
			return '';
		}
	}

function fixOldVersionQuizData(){
	try{
		if(typeof QuizData.questions[0].choices[0].favoursResult == "string") {
			//Is old version - has only single favoured result per choice (specified with id)
			_.each(QuizData.questions, function(question){
				_.each(question.choices, function(choice){
					choice.favoursResult = [{result : choice.favoursResult, weightage : choice.favouredResultWeightage}];
					delete choice.favouredResultWeightage;
				});
			});
		}
	} catch(e){
	}
}

fixOldVersionQuizData();
