"use client"

import React, { useState, useEffect } from "react";
import { useSearchParams } from 'next/navigation';
import 'react-range-slider-input/dist/style.css';
import "react-responsive-carousel/lib/styles/carousel.min.css";
import {fetchTitles, fetchTitleCount, fetchTitleById} from "@/app/requests";
import Title from "@/app/titles/components";

export default function Page() {
    const [titles, setTitles] = useState([]);
    const [titleCount, setTitleCount] = useState(0);
    const [title, setTitle] = useState(null);

    const searchParams = useSearchParams();

    let offset = 0;
    if (searchParams.has("offset")) {
        offset = searchParams.get("offset");
    }
    let limit = 8;
    if (searchParams.has("limit")) {
        limit = searchParams.get("limit");
    }
    let id = null;
    if (searchParams.has("id")) {
        id = searchParams.get("id");
    }

    // Titles
    useEffect(() => {
        async function fetchFilteredTitleCount() {
            const data = await fetchTitleCount();
            setTitleCount(data.title_count);
        }
        async function fetchFilteredTitles() {
            const data = await fetchTitles(offset, limit);
            setTitles(data);
        }

        if (!id) {
            fetchFilteredTitleCount();
            fetchFilteredTitles();
        }
    }, [id, offset, limit]);

    // Title
    useEffect(() => {
        async function fetchTitle(id) {
            let data = await fetchTitleById(id);
            setTitle(data);
        }

        if (id) {
            fetchTitle(id);
        }
    }, [id]);

    return  <div>
        {title ?
            <div>
                <div className="container m-2">
                    <div className="row">
                        <h2>{title.original_title}</h2>
                        <Title id={title.id} link={false} />
                    </div>
                </div>
            </div>
            :
            <div>
                <h1 className="text-center my-2">{titleCount} Titles</h1>

                <div className="container p-5">
                    <div className="row">
                        <div className="col-3">
                            <label htmlFor="title-input">Title:</label>
                            <input id="title-input" type="text" className="form-control"/>
                        </div>
                    </div>
                </div>

                <div className="row row-cols-1 row-cols-md-4">
                    {titles.map((v, index) =>
                        <Title key={index} titleData={v} link={true}/>
                    )}
                </div>
            </div>
        }
    </div>
}
