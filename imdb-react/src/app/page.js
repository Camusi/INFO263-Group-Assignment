"use client";

export default function Page() {

    // Return the HTML
    return <div className="row">
        <div className="row justify-content-center my-4">
            <img className="img-thumbnail" src="/images/yoda.jpeg" alt="Yoda"/>
            <h4 className="text-center">Yoda: “Looking? Found someone you have, eh?”</h4>
        </div>

        <div className="row align-middle align-items-center py-2">
            <div className="offset-2 col-7 align-middle">
                <input id="search-input" className="form-control" type="text" name="search"
                       placeholder="Search for a Film, Serie, Person, ..."/>
            </div>

            <div className="col-2 d-grid col-2">
                <button id="search-button" type="submit" className="btn btn-warning">Search</button>
            </div>
        </div>

    </div>
}
