"use client"

import 'bootstrap/dist/css/bootstrap.css';
import './css/style.css';
import { Inter } from 'next/font/google';
import Link from 'next/link'
import {useEffect} from "react";


const inter = Inter({ subsets: ['latin'] })

export default function RootLayout({ children }) {

  useEffect(() => {
    require("bootstrap/dist/js/bootstrap.bundle.min.js");
    // require("/leaflet/dist/leaflet.js");
  },[]);

  return (
    <html lang="en">
      <head>
        <title>IMDB 2</title>
      </head>
      <body>
        <main role="main" className="container bg-light">
          <nav className="navbar navbar-expand-lg bg-yellow-500">
            <div className="container-fluid">
              <Link className="navbar-brand p-2" href="/">IMDB 2</Link>
              <div className="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div className="navbar-nav">
                  <a className="nav-link p-2" href="titles">Titles</a>
                  <a className="nav-link p-2" href="#">People</a>
                </div>
              </div>
            </div>
          </nav>

          {children}

        </main>
      </body>
    </html>
  )
}
