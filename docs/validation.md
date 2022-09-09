# Validation

When using the file manager, you can implement your custom business logic to handle the upload validation, To do so, you
may use the `uploadRules()` method that takes a similar input as the classic `rules()` method, for instance, you may
want to check the mime type of the uploaded file, you can then use something like this :

```php
// app/Nova/Project.php

use BBSLab\NovaFileManager\FileManager;

class Project extends Resource
{
    // ...

    public function fields(NovaRequest $request): array
    {
        return [
            // ... any other fields
            FileManager::make(__('Attachments'), 'attachments')
                ->uploadRules('mimes:pdf,png,jpg'),
        ];
    }
}
```

You can take this a notch further by having advanced validation on your field, you may use the `validateUploadUsing()`
method to define a full-on custom businness logic :

```php
// app/Nova/Project.php

use BBSLab\NovaFileManager\FileManager;
use BBSLab\NovaFileManager\Http\Requests\UploadFileRequest;
use Illuminate\Http\UploadedFile;

class Project extends Resource
{
    // ...

    public function fields(NovaRequest $request): array
    {
        return [
            // ... any other fields
            FileManager::make(__('Attachments'), 'attachments')
                ->validateUploadUsing(function(UploadFileRequest $request, UploadedFile $uploadedFile, array $getID3_metadata, bool $uploadComplete){
                    // $getID3_metadata here is an array of all the metadata that was extracted from the uploaded file by getID3
                    
                    // $uploadComplete is a boolean that tells you if the upload is complete or not, since we're using chunked uploads, we need to wait for all the chunks to be collected for this boolean to be true, otherwise it will be false, and you'll be performing your validation on a chunked file which is not what you want
                    
                    if(data_get($getID3_metadata, 'fileformat') !== 'mp4'){
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'file' => __('You are supposed to upload an mp4 file'),
                        ]);
                        
                        // (or) return false
                    }
                    
                    return true;
                }),
        ];
    }
}
```