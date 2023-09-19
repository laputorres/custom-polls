jQuery(document).ready(function ($) {
  var colors = ["#00dcd2"];
  var colorIndex = 0;
  var currentPollIndex = 0;
  var pollCards = $(".poll-card");

  pollCards.hide();
  pollCards.eq(currentPollIndex).show();

  $(".poll-answer-button").on("click", function () {
    var pollId = $(this).closest(".poll-form").data("poll-id");
    var selectedAnswer = $(this).data("answer");
    var postId = $(this).closest(".poll-form").data("post-id");
    var pollCardContainer = $(this).closest(".poll-card");

    pollCardContainer.find(".poll-answer-button").prop("disabled", true);

    $.ajax({
      url: custom_polls.ajax_url,
      type: "POST",
      data: {
        action: "vote_poll",
        post_id: postId,
        poll_id: pollId,
        selected_option: selectedAnswer,
      },
      success: function (response) {
        var data = JSON.parse(response);
        var percentages = data.percentages;
        var totalVotes = data.total_votes;

        pollCardContainer.find(".poll-answer-container").each(function () {
          var answer = $(this).find(".poll-answer-button").data("answer");
          var percentage = percentages[answer];
          var answerButton = $(this).find(".poll-answer-button");

          if (percentage === undefined) {
            percentage = 0;
          }

          answerButton.html(answer + " (" + percentage + "%)");

          answerButton.css(
            "background",
            "linear-gradient(to right, " +
              colors[colorIndex] +
              " " +
              percentage +
              "%, transparent " +
              percentage +
              "%)"
          );

          colorIndex = (colorIndex + 1) % colors.length;
        });

        pollCardContainer
          .find(".total-votes-count")
          .text(totalVotes + " votes");
      },
      error: function () {
        pollCardContainer.find(".poll-answer-button").prop("disabled", false);
      },
    });
  });

  $(".prev-poll-button").on("click", function () {
    currentPollIndex =
      (currentPollIndex - 1 + pollCards.length) % pollCards.length;
    pollCards.hide();
    pollCards.eq(currentPollIndex).show();
  });

  $(".next-poll-button").on("click", function () {
    currentPollIndex = (currentPollIndex + 1) % pollCards.length;
    pollCards.hide();
    pollCards.eq(currentPollIndex).show();
  });

  $(".delete-poll-button").on("click", function () {
    var postId = $(this).data("post-id");
    var pollId = $(this).data("poll-id");

    if (confirm("Are you sure you want to delete this poll?")) {
      $.ajax({
        url: custom_polls.ajax_url,
        type: "POST",
        data: {
          action: "delete_poll",
          post_id: postId,
          poll_id: pollId,
        },
        success: function (response) {
          location.reload();
          alert("Answer delete successfully");
        },
        error: function () {
          location.reload();
        },
      });
    }
  });

  $(".edit-answer-button").on("click", function () {
    var postId = $(this).data("post-id");
    var pollId = $(this).data("poll-id");
    var pollCardContainer = $(this).closest(".poll-card_dashboard"); // Obtener el contenedor de la tarjeta

    var editedQuestion = pollCardContainer.find(".poll-question").val();
    var editedAnswers = [];
    pollCardContainer.find("ul.poll-answers li input").each(function () {
      var editedAnswer = $(this).val();
      editedAnswers.push(editedAnswer);
    });

    $.ajax({
      url: custom_polls.ajax_url,
      type: "POST",
      data: {
        action: "edit_poll_question_and_answers", // Cambiar el nombre de la acción si es necesario
        post_id: postId,
        poll_id: pollId,
        edited_question: editedQuestion,
        edited_answers: editedAnswers,
      },
      success: function (response) {
        location.reload();
      },
      error: function () {
        console.log("Error editing question and answers");
      },
    });
  });
});
