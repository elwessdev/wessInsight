import { startStimulusApp } from '@symfony/stimulus-bundle';
import FileUploadController from './controllers/file_upload_controller.js';

const app = startStimulusApp();
// register any custom, 3rd party controllers here
app.register('file-upload', FileUploadController);
