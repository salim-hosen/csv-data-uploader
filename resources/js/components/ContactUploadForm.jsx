import axios from 'axios';
import React, { useEffect, useState } from 'react';
import ReactDOM from 'react-dom/client';

function ContactUploadForm() {

    const [csv, setCsv] = useState(null);
    const [loading, setLoading] = useState(false);
    const [errors, setErrors] = useState({});

    const [batchSummary, setBatchSummary] = useState({});
    const [uploadSummary, setUploadSummary] = useState({});

    const [show, setShow] = useState('loading');

    const uploadCsv = async () => {

        try {
            if(!csv){
                setErrors({
                    csv: "Please Upload a File"
                });
                return;
            }
            setErrors({});
            setLoading(true);
            const formData = new FormData();
            if (csv) formData.append("csv", csv);
            const response = await axios.post("/api/upload", formData);
            console.log(response);
            if(response.data){
                setBatchSummary(response.data);
                setShow("progress");
                localStorage.setItem("batch_id", response.data.id);
                startInterval();
            }
            setLoading(false);
        } catch (err) {
            if (err.response.status != 422) throw err;
            setErrors(err.response.data.errors);
            setLoading(false);
        }

    }

    // const deleteJob = async () => {
    //     try {

    //         const batch_id = localStorage.getItem("batch_id");
    //         const response = await axios.delete("/api/upload/"+batch_id);
    //         console.log(response.data);
    //         setBatchSummary(null);

    //     } catch (err) {
    //         console.log(err);
    //     }
    // }


    let interval = null;
    const startInterval = () => {
        interval = setInterval(function(){
            fetchRunningUpload();
        }, 5000);

    }

    const fetchUploadSummary = async () => {
        try {

            const batch_id = localStorage.getItem("batch_id");
            const response = await axios.get("/api/upload-summary/"+batch_id);
            setUploadSummary(response.data.data);
            setShow("summary");
            clearInterval(interval);
            localStorage.removeItem("batch_id");

        } catch (err) {
            console.log(err);
        }
    }

    const fetchRunningUpload = async () => {
        try {

            const batch_id = localStorage.getItem("batch_id");
            const response = await axios.get("/api/upload/"+batch_id);
            if(response.data){
                setBatchSummary(response.data);
                if(response.data.progress == 100){
                    fetchUploadSummary();
                }else{
                    if(show != "progress") setShow("progress");
                }
            }else{
                if(show != "form") setShow("form");
            }

        } catch (err) {
            console.log(err);
            localStorage.removeItem("batch_id");
            clearInterval(interval);
            if(show != "form") setShow("form");
        }
    }

    useEffect(() => {
        fetchRunningUpload();
        const batch_id = localStorage.getItem("batch_id");
        if(batch_id){
            startInterval();
        }
    }, []);

    return (
        show == "loading" ?
        <div className='text-center w-100 my-3'>
            <i className='fas fa-spinner fa-spin h3'></i>
        </div>
        :
        <div className="container">
            <div className="row justify-content-center">
                <div className="col-md-5">
                    <div className='card border-0 shadow-sm'>
                        <div className="card-body">

                            {
                                show == "form" && <div>
                                    <div className="mb-3">
                                        <label htmlFor="formFile" className="form-label">Upload CSV File</label>
                                        <input className={`form-control ${errors.csv ? 'is-invalid' : ''}`} type="file" id="formFile" onChange={(e) => setCsv(e.target.files[0])} />
                                        {errors.csv && <div className='invalid-feedback'>{errors.csv}</div>}
                                    </div>
                                    <div className='text-center'>
                                        <button onClick={uploadCsv} type='button' className="btn btn-primary px-5" disabled={loading}>
                                            {loading && <i className="fa-solid fa-spinner fa-spin me-2"></i>}
                                            Upload
                                        </button>
                                    </div>
                                </div>
                            }

                            {
                                show == "progress" &&   <div>
                                    <div className='d-flex align-items-center justify-content-between'>
                                        <p>Upload is In Progress ({batchSummary.progress}%)</p>

                                        {/*
                                        {batchSummary.total_jobs == batchSummary.failed_jobs ? <span className='text-danger'>Failed</span> : ''}
                                        <button type='button' className='btn btn-danger btn-sm' onClick={deleteJob}>
                                            <i className='fas fa-trash'></i>
                                        </button> */}
                                    </div>
                                    <div className="progress" role="progressbar">
                                        <div className="progress-bar bg-success progress-bar-striped progress-bar-animated" style={{ width: `${batchSummary.progress}%` }}>{batchSummary.progress ?? 0}%</div>
                                    </div>
                                    <small className='small'>Upload will be running even if the window is closed</small>

                                </div>
                            }

                            {
                                show == "summary" && <table className='table'>
                                    <thead>
                                        <tr>
                                            <th colSpan={2} className='text-center'>
                                                <h4>Upload Summary</h4>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th>Total Data</th>
                                            <td>{uploadSummary.total_data}</td>
                                        </tr>
                                        <tr>
                                            <th>Total Successful</th>
                                            <td>{uploadSummary.total_successful}</td>
                                        </tr>
                                        <tr>
                                            <th>Total Duplicate</th>
                                            <td>{uploadSummary.total_duplicate}</td>
                                        </tr>
                                        <tr>
                                            <th>Total Invalid</th>
                                            <td>{uploadSummary.total_invalid}</td>
                                        </tr>
                                        <tr>
                                            <th>Total Incomplete</th>
                                            <td>{uploadSummary.total_incomplete}</td>
                                        </tr>
                                        <tr>
                                            <td colSpan={2} className='text-center border-0'>
                                                <button type='button' className='btn btn-primary btn-sm w-100 mt-3' onClick={ () => setShow("form") }>Back to Upload</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            }
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}

export default ContactUploadForm;

if (document.getElementById('contact-upload-form')) {
    const Index = ReactDOM.createRoot(document.getElementById("contact-upload-form"));

    Index.render(
        // <React.StrictMode>
        //     <ContactUploadForm />
        // </React.StrictMode>
        <ContactUploadForm />
    )
}
