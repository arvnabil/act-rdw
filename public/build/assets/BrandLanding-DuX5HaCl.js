import{j as e,L as j,R as p,H as N}from"./app-8jlO-GOR.js";import{g as k,M as S}from"./MainLayout-B_NMIF89.js";import{S as h,a as x,W as _}from"./WorkShowcaseSection-2wp3uam9.js";import{A as w,N as v,P}from"./effect-fade-Cql-zif5.js";import{S as y}from"./SectionTitle-DHTxUSIO.js";/* empty css            */import"./Toast-BZmhIwvk.js";function C({brand:t,pageData:r,relatedServices:i,getImageUrl:c,setLightboxImage:s}){const o=(a,n)=>{a.preventDefault();const d=document.querySelector(n);if(d){const g=document.body.getBoundingClientRect().top,l=d.getBoundingClientRect().top-g-100;window.scrollTo({top:l,behavior:"smooth"})}};return e.jsxs("section",{children:[e.jsxs("div",{className:"brand-hero-area position-relative",style:{backgroundColor:r.hero_styles?.background_color||"#E8B4B4",backgroundImage:r.hero_styles?.background_image?`url(${c(r.hero_styles.background_image)})`:"none",backgroundSize:"cover",backgroundPosition:"center",padding:"100px 0",minHeight:"500px",display:"flex",alignItems:"center"},children:[e.jsx("div",{className:"position-absolute top-0 start-0 w-100 h-100",style:{backgroundColor:"rgba(15, 23, 42, 0.6)",zIndex:0}}),e.jsx("div",{className:"container th-container position-relative",style:{zIndex:1},children:e.jsxs("div",{className:"row align-items-center",children:[e.jsx("div",{className:"col-lg-6",children:e.jsxs("div",{className:"hero-content wow fadeInUp",children:[e.jsx("span",{className:"sub-title text-uppercase mb-20",style:{letterSpacing:"2px"},children:"Business Solution"}),e.jsx("h1",{className:"hero-title text-anime-style-3 mb-4",style:{fontSize:"clamp(2rem, 5vw, 4rem)",fontWeight:"bold",color:"white",lineHeight:"1.2"},children:t.name.toUpperCase()}),e.jsx("p",{className:"hero-text mb-30",style:{fontSize:"1.2rem",maxWidth:"500px",color:"rgba(255,255,255,0.8)"},children:"Explore video conferencing products, including conference cameras, room solutions, webcams, headsets, collaboration tools, and accessories."}),e.jsxs("a",{href:"#products",className:"th-btn style3 th-radius th-icon",children:["Contact Sales"," ",e.jsx("i",{className:"fa-regular fa-arrow-right ms-2"})]})]})}),e.jsx("div",{className:"col-lg-6",children:e.jsxs("div",{className:"hero-scroll-cards wow fadeInRight",style:{maxWidth:"800px",marginLeft:"auto"},children:[e.jsx("div",{className:"mb-4 ps-0 ps-lg-2 text-center text-lg-start",children:e.jsxs("span",{className:"sub-title style1 text-anime-style-2 text-white",children:[e.jsx("span",{className:"squre-shape left me-3 bg-white"}),"Award & Certified",e.jsx("span",{className:"squre-shape d-lg-none right ms-3 bg-white"})]})}),e.jsx(h,{modules:[w],spaceBetween:20,autoplay:{delay:2500,disableOnInteraction:!1},loop:!0,breakpoints:{0:{slidesPerView:1},768:{slidesPerView:2},1024:{slidesPerView:3}},className:"hero-badge-slider",children:[1,2,3,4].map(a=>e.jsx(x,{children:e.jsx("div",{className:"bg-white d-flex align-items-center justify-content-center shadow-lg",style:{width:"250px",height:"250px",padding:"20px",margin:"0 auto",borderRadius:"10px",cursor:"pointer"},onClick:()=>s("https://activ.co.id/wp-content/uploads/2024/11/elite-dk-360x360-1.png"),children:e.jsx("img",{src:"https://activ.co.id/wp-content/uploads/2024/11/elite-dk-360x360-1.png",alt:"Elite Partner",className:"img-fluid",style:{maxHeight:"100%",maxWidth:"100%",objectFit:"contain"}})})},a))})]})})]})})]}),e.jsx("div",{className:"brand-subnav py-3",style:{backgroundColor:"#1DA2C0"},children:e.jsx("div",{className:"container th-container",children:e.jsx("div",{className:"d-flex justify-content-center gap-4 flex-wrap",children:(()=>{const a=[];return i&&i.length>0?i.forEach(n=>{a.push({name:n.name,link:`#service-${n.slug}`})}):a.push({name:"Solutions",link:"#room-solutions"}),a.push({name:"Kategori",link:"#all-categories"},{name:"New Product",link:"#latest-products"},{name:"Project",link:"#project-showcase"}),a.map((n,d)=>e.jsx("a",{href:n.link,onClick:m=>o(m,n.link),className:"text-white text-uppercase fw-bold text-decoration-none fs-6 hover-opacity",children:n.name},d))})()})})})]})}function B({subTitle:t,title:r,align:i="text-center text-lg-start",mb:c="mb-20"}){return e.jsxs("div",{className:`title-area ${i} ${c}`,children:[t&&e.jsxs("span",{className:"sub-title style1 text-anime-style-2",children:[e.jsx("span",{className:"squre-shape left me-3"}),t,e.jsx("span",{className:"squre-shape d-lg-none right ms-3"})]}),e.jsx("h2",{className:"sec-title text-white",children:r})]})}function $({relatedServices:t,getImageUrl:r,brand:i}){if(!t||t.length===0)return null;const c=s=>s?.slug||s?.name?.toLowerCase();return e.jsx(e.Fragment,{children:t.map((s,o)=>e.jsxs("section",{className:"position-relative space-top space-extra-bottom",id:`service-${s.slug}`,style:{backgroundImage:`url(${s.image?r(s.image):"/assets/img/bg/bg_overlay_1.png"})`,backgroundSize:"cover",backgroundPosition:"center"},children:[e.jsx("div",{className:"position-absolute top-0 start-0 w-100 h-100",style:{background:"linear-gradient(to bottom, #0A141E 0%, rgba(10, 20, 30, 0.85) 15%, rgba(10, 20, 30, 0.85) 85%, #0A141E 100%)",zIndex:1}}),e.jsxs("div",{className:"container th-container position-relative",style:{zIndex:2},children:[e.jsx("div",{className:"row justify-content-center",children:e.jsx("div",{className:"col-xl-8",children:e.jsx("div",{className:"title-area text-center mb-55",children:e.jsx(B,{subTitle:s.sub_title,title:s.title,align:"title-area service-title-box text-center",subTitleStyle:{backgroundColor:"transparent",color:"#20B2AA"}})})})}),e.jsxs("div",{className:"slider-area",children:[e.jsx(h,{modules:[v,P],spaceBetween:30,slidesPerView:1,pagination:{el:`.service-pagination-${o}`,type:"progressbar"},navigation:{prevEl:`.service-prev-${o}`,nextEl:`.service-next-${o}`},onBeforeInit:a=>{a.params.navigation.prevEl=`.service-prev-${o}`,a.params.navigation.nextEl=`.service-next-${o}`},breakpoints:{576:{slidesPerView:1},768:{slidesPerView:2},992:{slidesPerView:3},1200:{slidesPerView:3}},className:"service-slider",children:s.solutions.map((a,n)=>e.jsx(x,{children:e.jsx("div",{className:"room-card position-relative overflow-hidden rounded-20 bg-dark",children:e.jsxs("div",{className:"room-img position-relative overflow-hidden room-img-height",style:{borderRadius:"20px"},children:[e.jsx("img",{src:a.image?r(a.image):"/assets/img/logo/activ-logo-white.png",onError:d=>{d.target.onerror=null,d.target.src="https://placehold.co/600x400?text=No+Image"},alt:a.title,className:"w-100 h-100 object-fit-cover",style:{transition:"transform 0.5s ease"}}),e.jsxs("div",{className:"position-absolute bottom-0 start-0 w-100 p-4",style:{background:"linear-gradient(to top, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0.4) 60%, transparent 100%)",zIndex:2},children:[e.jsx("h3",{className:"text-white mb-2 h4 fw-bold",children:a.title}),e.jsx("p",{className:"text-white-50 mb-4 fs-xs",children:a.desc}),e.jsxs(j,{href:`/${c(i)}/products?service_item=${a.slug}`,className:"th-btn style3 th-radius th-icon room-btn btn-ghost-green",children:["Show Products"," ",e.jsx("i",{className:"fa-regular fa-arrow-right ms-2"})]})]})]})})},n))}),e.jsxs("div",{className:"d-none d-md-flex align-items-center justify-content-center gap-4 mt-5",children:[e.jsx("button",{className:`service-prev-${o} icon-btn style2 bg-transparent border rounded-circle border-white text-white hover-white`,children:e.jsx("i",{className:"fa-regular fa-arrow-left"})}),e.jsx("div",{className:`service-pagination-${o} position-relative`,style:{width:"150px",height:"2px",background:"rgba(255,255,255,0.2)",borderRadius:"2px",overflow:"hidden"}}),e.jsx("button",{className:`service-next-${o} icon-btn style2 bg-transparent border rounded-circle border-white text-white hover-white`,children:e.jsx("i",{className:"fa-regular fa-arrow-right"})})]})]})]})]},o))})}function E({categories:t,brand:r,getBrandSlug:i,getImageUrl:c}){return e.jsx("section",{className:"space",id:"all-categories",children:e.jsxs("div",{className:"container th-container",children:[e.jsx("div",{className:"title-area text-start mb-40",children:e.jsx(y,{subTitle:"Kategori",title:"Semua Kategori",align:"title-area service-title-box text-center"})}),e.jsx("div",{className:"row justify-content-center gy-4 row-cols-2 row-cols-md-4 row-cols-lg-6",children:t&&t.length>0?t.map((s,o)=>e.jsx("div",{className:"col",children:e.jsx(j,{href:`/${i(r)}/products?category=${s.slug||s.id}`,children:e.jsxs("div",{className:"category-card py-4 px-2 text-center h-100 d-flex flex-column align-items-center justify-content-center",style:{backgroundColor:"#F3F4F6",borderRadius:"10px",transition:"all 0.3s ease",cursor:"pointer",minHeight:"220px"},children:[e.jsx("div",{className:"cat-img-wrapper mb-3 d-flex align-items-center justify-content-center",style:{height:"160px",width:"100%"},children:e.jsx("img",{src:c(s.image),alt:s.name,style:{maxHeight:"100%",maxWidth:"100%",objectFit:"contain"}})}),e.jsx("h6",{className:"fw-medium text-dark mb-0",style:{fontSize:"0.9rem"},children:s.name})]})})},o)):e.jsx("div",{className:"col-12 text-center text-muted",children:"No categories found."})})]})})}function z({products:t,getImageUrl:r}){return e.jsx("section",{className:"space-top space-extra-bottom bg-white",id:"latest-products",children:e.jsxs("div",{className:"container th-container",children:[e.jsx(y,{subTitle:"New",title:"Products",align:"text-center"}),e.jsx(h,{modules:[v,w],spaceBetween:24,slidesPerView:1,autoplay:{delay:3e3,disableOnInteraction:!1},breakpoints:{575:{slidesPerView:2},992:{slidesPerView:3},1200:{slidesPerView:4}},className:"latest-products-slider",style:{paddingBottom:"40px"},children:t&&t.length>0?t.map((i,c)=>e.jsx(x,{children:e.jsxs("div",{className:"product-card-simple latest-product-height position-relative overflow-hidden w-100",style:{borderRadius:"30px",boxShadow:"0 10px 30px rgba(0,0,0,0.3)",cursor:"pointer"},children:[e.jsx("img",{src:r(i.image_path),alt:i.name,className:"w-100 h-100 object-fit-cover",style:{transition:"transform 0.5s ease"}}),e.jsxs("div",{className:"product-content position-absolute bottom-0 start-0 w-100 p-4 d-flex flex-column justify-content-end",style:{height:"100%",background:"linear-gradient(to top, #111 0%, rgba(17,17,17,0.8) 30%, transparent 100%)"},children:[e.jsx("h5",{className:"text-white mb-1 fw-bold",children:i.name}),i.category&&e.jsx("p",{className:"text-white-50 fs-xs mb-0 fw-medium",children:i.category})]})]})},c)):e.jsx("div",{className:"text-muted",children:"No latest products found."})})]})})}const F=({brand:t,products:r,categories:i,pageSite:c,relatedServices:s})=>{const[o,a]=p.useState(null),[n,d]=p.useState(null);p.useEffect(()=>{document.querySelectorAll(".scroll-text-ani").forEach(b=>{k.to(b,{backgroundImage:"linear-gradient(to right, #0B1422 100%, #D5D7DA 100%)",ease:"none",scrollTrigger:{trigger:b,start:"top bottom",end:"top center",scrub:!0}})})},[]);const m=l=>l?l.startsWith("http")||l.startsWith("/assets")?l:`/storage/${l}`:"",g=l=>l.slug||l.name.toLowerCase(),u=c||{hero_styles:{background_color:"#444A572C",background_image:"https://activ.co.id/wp-content/uploads/2023/11/Group-97-1.png?id=5807"}},f=[{title:"Purnomo",category:"Logitech",image:"/assets/img/project/project_3_9_.jpg"},{title:"Project Alpha",category:"Yealink",image:"/assets/img/project/project_3_9_.jpg"},{title:"Meeting Room B",category:"Logitech",image:"/assets/img/project/project_3_9_.jpg"},{title:"Conference Hall",category:"Jabra",image:"/assets/img/project/project_3_9_.jpg"},{title:"Huddle Space",category:"Maxhub",image:"/assets/img/project/project_3_9_.jpg"},{title:"Executive Office",category:"Zoom",image:"/assets/img/project/project_3_9_.jpg"}];return e.jsxs(S,{children:[e.jsx(N,{title:t.name}),e.jsx(C,{brand:t,pageData:u,relatedServices:s,getImageUrl:m,setLightboxImage:d}),e.jsx($,{relatedServices:s,getImageUrl:m,brand:t}),e.jsx(E,{categories:i,brand:t,getBrandSlug:g,getImageUrl:m}),e.jsx(z,{products:r,getImageUrl:m}),e.jsx(_,{staticShowcase:f}),e.jsx("style",{children:`
                .hover-opacity:hover {
                    opacity: 0.7;
                }
                .hover-white:hover {
                    background-color: rgba(255, 255, 255, 0.1) !important;
                    border-color: #ffffff !important;
                    color: #ffffff !important;
                }
                .hover-dark:hover {
                    background-color: rgba(0, 0, 0, 0.1) !important;
                    border-color: #000000 !important;
                    color: #000000 !important;
                }
                .room-card:hover img {
                    transform: scale(1.1);
                }
                .room-card {
                    overflow: hidden;
                    backface-visibility: hidden;
                }
                .category-card:hover {
                    background-color: #E5E7EB !important;
                    transform: translateY(-5px);
                }
                .product-card-simple:hover img {
                    transform: scale(1.05) !important;
                }
                .room-img-height {
                    height: 500px;
                }
                .room-btn {
                    padding: 10px 25px;
                }
                .latest-product-height {
                    height: 400px;
                }
                @media (max-width: 576px) {
                    .room-img-height {
                        height: 380px;
                    }
                    .room-btn {
                        padding: 8px 15px !important;
                        font-size: 14px;
                        width: 100%;
                        justify-content: center;
                    }
                    .latest-product-height {
                        height: 350px;
                    }
                }
                .swiper-pagination-progressbar-fill {
                    background-color: #ffffff !important;
                }
                .swiper-pagination-lock {
                    display: none !important;
                }
                .btn-ghost-green {
                    background-color: transparent !important;
                    border: 1px solid #ffffff !important;
                    color: #ffffff !important;
                    transition: all 0.3s ease;
                    position: relative;
                    z-index: 1;
                }
                .btn-ghost-green:hover {
                    background-color: #28a745 !important;
                    border-color: #28a745 !important;
                    color: #ffffff !important;
                }
                .btn-ghost-green::before,
                .btn-ghost-green::after {
                    display: none !important;
                }
                .scroll-text-ani {
                    background-clip: text;
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                }
             `}),n&&e.jsxs("div",{className:"position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center",style:{zIndex:9999,backgroundColor:"rgba(0,0,0,0.9)"},onClick:()=>d(null),children:[e.jsx("button",{className:"btn-close btn-close-white position-absolute top-0 end-0 m-4",onClick:()=>d(null),"aria-label":"Close",style:{width:"2rem",height:"2rem",backgroundSize:"contain"}}),e.jsx("img",{src:n,alt:"Award Full View",className:"img-fluid",style:{maxHeight:"90vh",maxWidth:"90vw",objectFit:"contain",animation:"zoomIn 0.3s ease-out"},onClick:l=>l.stopPropagation()}),e.jsx("style",{children:`
                        @keyframes zoomIn {
                            from { transform: scale(0.9); opacity: 0; }
                            to { transform: scale(1); opacity: 1; }
                        }
                    `})]})]})};export{F as default};
