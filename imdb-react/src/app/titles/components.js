import Image from 'next/image'
import {useState, useEffect} from "react";
import {fetchTitleById} from "@/app/requests";

export default function Title(props) {
    const {titleData, id, link} = props;
    const [title, setTitle] = useState(null);

    // Fetch title
    useEffect(() => {
        async function fetchTitle() {
            let data = await fetchTitleById(id);
            setTitle(data);
        }

        if (titleData) {
            setTitle(titleData);
        } else if (id) {
            fetchTitle();
        }
    }, [id])

    if (title) {
        let title_component = <div className="card">
            <img className="card-img-top img-thumbnail" src='/images/movie_not_found.jpg'
                 alt={title.original_title}/>
            <div className="card-body">
                <div>{title.original_title}</div>
                <div>Rating: {title.rating}</div>
                <div>Runtime: {title.runtime_minutes} min.</div>
                <div>Year: {title.start_year}</div>
                <div>Principals: {title.principals_count}</div>
                <div>Directors: {title.directors_count}</div>
                <div>Writers: {title.writers_count}</div>
            </div>
        </div>

        if (link) {
            return <a className="col-4 p-2" href={"titles?id=" + title.id}>
                {title_component}
            </a>
        } else {
            return title_component;
        }
    }
    return <div>Loading...</div>
}