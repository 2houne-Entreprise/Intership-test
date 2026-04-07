Route::middleware('auth')->group(function() {
    Route::resource('projects', ProjectController::class);
});
