function handleMoveLeftToRight() {
	document.getElementById('teamschanges_check').value = 1;
	move(document.getElementById('members'), document.getElementById('prediction_members'));
	selectAll(document.getElementById('prediction_members'));
}

function handleMoveRightToLeft() {
	document.getElementById('teamschanges_check').value = 1;
	move(document.getElementById('prediction_members'), document.getElementById('members'));
	selectAll(document.getElementById('prediction_members'));
}