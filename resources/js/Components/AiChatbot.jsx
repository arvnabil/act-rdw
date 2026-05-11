import React, { useState, useRef, useEffect } from 'react';
import axios from 'axios';

const aiAgent = {
    name: 'VION by ACTiV Assistant',
    role: 'ICT Solutions Consultant',
    avatar: '/images/vion.png',
    headerAvatar: '/images/avatar-vion.png',
    color: '#059669',
    gradient: 'linear-gradient(135deg, #ffffff, #f1f5f9)',
    actionGradient: 'linear-gradient(135deg, #10b981, #059669)',
};

const WA_NUMBER = '6281280944719'; 

const formatMarkdown = (text) => {
    if (!text) return '';
    const lines = text.split('\n');
    return lines.map((line, i) => {
        if (!line.trim()) return <div key={i} style={{ height: '8px' }} />;
        
        // Handle Headings
        if (line.startsWith('### ')) return <h3 key={i} style={{ margin: '10px 0 5px 0', fontSize: '1.1em', fontWeight: 'bold', color: '#fff' }}>{line.slice(4)}</h3>;
        if (line.startsWith('## ')) return <h2 key={i} style={{ margin: '12px 0 6px 0', fontSize: '1.2em', fontWeight: 'bold', color: '#fff' }}>{line.slice(3)}</h2>;
        
        // Handle Bullet Lists (* or -)
        if (line.trim().startsWith('* ') || line.trim().startsWith('- ')) {
            const content = line.trim().slice(2);
            return (
                <div key={i} style={{ display: 'flex', gap: '8px', marginBottom: '4px', paddingLeft: '4px' }}>
                    <span style={{ color: aiAgent.color }}>•</span>
                    <span>{parseInlineFormatting(content)}</span>
                </div>
            );
        }

        return <div key={i} style={{ marginBottom: '4px' }}>{parseInlineFormatting(line)}</div>;
    });
};

const parseInlineFormatting = (text) => {
    const parts = text.split(/(\*\*.*?\*\*|\*.*?\*)/g);
    return parts.map((part, i) => {
        if (part.startsWith('**') && part.endsWith('**')) return <strong key={i}>{part.slice(2, -2)}</strong>;
        if (part.startsWith('*') && part.endsWith('*')) return <em key={i}>{part.slice(1, -1)}</em>;
        return part;
    });
};


const ProductCard = ({ product }) => {
    const defaultImg = '/assets/default.png';
    return (
        <a href={product.link} target="_blank" rel="noopener noreferrer" style={{
            flexShrink: 0, width: '180px', background: 'rgba(255,255,255,0.05)', borderRadius: '16px', 
            border: '1px solid rgba(255,255,255,0.1)', overflow: 'hidden', backdropFilter: 'blur(10px)',
            display: 'flex', flexDirection: 'column', textDecoration: 'none', transition: 'all 0.2s', cursor: 'pointer'
        }} onMouseEnter={e => e.currentTarget.style.transform = 'translateY(-4px)'}
           onMouseLeave={e => e.currentTarget.style.transform = 'translateY(0)'}>
            <div style={{ width: '100%', height: '110px', background: '#fff', position: 'relative', display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
                <img 
                    src={product.image} 
                    onError={(e) => { e.target.onerror = null; e.target.src = defaultImg; }}
                    alt={product.name} 
                    style={{ width: '90%', height: '90%', objectFit: 'contain', padding: '10px' }} 
                />
                <div style={{ position: 'absolute', top: '8px', right: '8px', background: aiAgent.actionGradient, color: '#fff', fontSize: '9px', padding: '2px 8px', borderRadius: '10px', fontWeight: 'bold' }}>
                    READY
                </div>
            </div>
            <div style={{ padding: '12px', display: 'flex', flexDirection: 'column', gap: '8px', flex: 1 }}>
                <div style={{ fontSize: '12px', color: '#fff', fontWeight: '600', height: '36px', overflow: 'hidden', display: '-webkit-box', WebkitLineClamp: 2, WebkitBoxOrient: 'vertical', lineHeight: '1.4' }}>
                    {product.name}
                </div>
                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                    <div style={{ fontSize: '11px', color: 'rgba(255,255,255,0.5)' }}>Lihat Detail</div>
                    <div style={{ background: 'rgba(255,255,255,0.1)', color: '#fff', padding: '2px 6px', borderRadius: '6px', fontSize: '10px' }}>x{product.qty}</div>
                </div>
            </div>
        </a>
    );
};

const ProductList = ({ products }) => {
    const listRef = useRef(null);
    const isDown = useRef(false);
    const startX = useRef(0);
    const scrollLeft = useRef(0);

    useEffect(() => {
        const el = listRef.current;
        if (!el) return;

        const handleWheel = (e) => {
            if (e.deltaY !== 0) {
                e.preventDefault();
                e.stopPropagation();
                el.scrollLeft += e.deltaY;
            }
        };

        const handleMouseDown = (e) => {
            isDown.current = true;
            el.style.cursor = 'grabbing';
            startX.current = e.pageX - el.offsetLeft;
            scrollLeft.current = el.scrollLeft;
        };

        const handleMouseLeave = () => {
            isDown.current = false;
            el.style.cursor = 'grab';
        };

        const handleMouseUp = () => {
            isDown.current = false;
            el.style.cursor = 'grab';
        };

        const handleMouseMove = (e) => {
            if (!isDown.current) return;
            e.preventDefault();
            const x = e.pageX - el.offsetLeft;
            const walk = (x - startX.current) * 2; // Scroll speed
            el.scrollLeft = scrollLeft.current - walk;
        };

        el.addEventListener('wheel', handleWheel, { passive: false });
        el.addEventListener('mousedown', handleMouseDown);
        el.addEventListener('mouseleave', handleMouseLeave);
        el.addEventListener('mouseup', handleMouseUp);
        el.addEventListener('mousemove', handleMouseMove);

        return () => {
            el.removeEventListener('wheel', handleWheel);
            el.removeEventListener('mousedown', handleMouseDown);
            el.removeEventListener('mouseleave', handleMouseLeave);
            el.removeEventListener('mouseup', handleMouseUp);
            el.removeEventListener('mousemove', handleMouseMove);
        };
    }, []);

    return (
        <div 
            ref={listRef}
            className="product-list-container" 
            data-lenis-prevent
            style={{ 
                width: 'calc(100% + 32px)', 
                margin: '10px -16px 0 -16px', 
                display: 'flex', 
                flexDirection: 'row',
                flexWrap: 'nowrap',
                gap: '12px', 
                overflowX: 'auto', 
                padding: '4px 16px 12px 16px',
                scrollbarWidth: 'none',
                msOverflowStyle: 'none',
                WebkitOverflowScrolling: 'touch',
                cursor: 'grab',
                overscrollBehavior: 'none',
                userSelect: 'none'
            }}>
            {products.map((p, idx) => (
                <div key={idx} onDragStart={e => e.preventDefault()}>
                    <ProductCard product={p} />
                </div>
            ))}
        </div>
    );
};

const WhatsAppButton = ({ messages }) => {
    const [isSummarizing, setIsSummarizing] = useState(false);
    const handleWaClick = async (e) => {
        e.preventDefault();
        setIsSummarizing(true);
        try {
            const { data } = await axios.post('/api/ai/summarize', { history: messages.slice(-10) });
            const summary = data.summary || 'Tertarik diskusi lebih lanjut.';
            const text = `Halo tim Sales ACTiV, saya ingin diskusi lebih lanjut.\n\n*Rangkuman Chat (AI):*\n${summary}`;
            window.open(`https://wa.me/${WA_NUMBER}?text=${encodeURIComponent(text)}`, '_blank');
        } catch (err) {
            window.open(`https://wa.me/${WA_NUMBER}?text=Halo tim Sales ACTiV, saya ingin diskusi lebih lanjut.`, '_blank');
        } finally { setIsSummarizing(false); }
    };
    return (
        <button onClick={handleWaClick} disabled={isSummarizing} style={{
            display: 'inline-flex', alignItems: 'center', gap: '8px', background: '#25d366', color: '#fff', padding: '10px 16px',
            borderRadius: '12px', fontSize: '13px', fontWeight: '600', border: 'none', cursor: 'pointer', marginTop: '10px', width: '100%', justifyContent: 'center'
        }}>
            <span>{isSummarizing ? '⏳ Menyiapkan...' : '💬 Hubungi Tim Sales (WA)'}</span>
        </button>
    );
};

const getGreetingTime = () => {
    const hour = new Date().getHours();
    if (hour >= 4 && hour < 11) return 'Pagi';
    if (hour >= 11 && hour < 15) return 'Siang';
    if (hour >= 15 && hour < 19) return 'Sore';
    return 'Malam';
};

const LeadForm = ({ onComplete }) => {
    const [formData, setFormData] = useState({ name: '', whatsapp: '', email: '' });
    const [isSubmitting, setIsSubmitting] = useState(false);

    const handleSubmit = async (e) => {
        e.preventDefault();
        setIsSubmitting(true);
        try {
            const { data } = await axios.post('/api/ai/start-session', formData);
            if (data.success) { onComplete(data.session_id, formData); }
        } catch (err) {
            alert('Gagal memulai sesi. Silakan coba lagi.');
        } finally { setIsSubmitting(false); }
    };

    return (
        <div style={{ padding: '24px', display: 'flex', flexDirection: 'column', gap: '16px', color: '#fff' }}>
            <div style={{ textAlign: 'center', marginBottom: '8px' }}>
                <h3 style={{ margin: 0, fontSize: '18px' }}>Halo, Selamat {getGreetingTime()}! 👋</h3>
                <p style={{ margin: '4px 0 0', fontSize: '13px', opacity: 0.8 }}>Boleh tahu siapa yang kami ajak bicara?</p>
            </div>
            <form onSubmit={handleSubmit} style={{ display: 'flex', flexDirection: 'column', gap: '12px' }}>
                <input required placeholder="Nama Lengkap" value={formData.name} onChange={e => setFormData({...formData, name: e.target.value})}
                       style={{ background: 'rgba(255,255,255,0.06)', border: '1px solid rgba(255,255,255,0.1)', borderRadius: '12px', padding: '12px', color: '#fff', outline: 'none' }} />
                <input required placeholder="No. WhatsApp (Contoh: 0812...)" value={formData.whatsapp} onChange={e => setFormData({...formData, whatsapp: e.target.value})}
                       style={{ background: 'rgba(255,255,255,0.06)', border: '1px solid rgba(255,255,255,0.1)', borderRadius: '12px', padding: '12px', color: '#fff', outline: 'none' }} />
                <input type="email" placeholder="Email (Opsional)" value={formData.email} onChange={e => setFormData({...formData, email: e.target.value})}
                       style={{ background: 'rgba(255,255,255,0.06)', border: '1px solid rgba(255,255,255,0.1)', borderRadius: '12px', padding: '12px', color: '#fff', outline: 'none' }} />
                <button type="submit" disabled={isSubmitting} style={{
                    background: aiAgent.actionGradient, color: '#fff', border: 'none', borderRadius: '12px', padding: '12px', fontWeight: '700', cursor: 'pointer', marginTop: '8px'
                }}>
                    {isSubmitting ? '⏳ Menyiapkan...' : 'Mulai Konsultasi'}
                </button>
            </form>
        </div>
    );
};


const MessageBubble = ({ msg, allMessages }) => {
    const isUser = msg.role === 'user';
    const hasTrigger = !isUser && msg.content.includes('[WA_TRIGGER]');
    const cleanContent = !isUser ? msg.content.replace('[WA_TRIGGER]', '').trim() : msg.content;

    return (
        <div style={{ display: 'flex', flexDirection: 'column', alignItems: isUser ? 'flex-end' : 'flex-start', marginBottom: '16px', width: '100%' }}>
            <div style={{ display: 'flex', justifyContent: isUser ? 'flex-end' : 'flex-start', gap: '8px', alignItems: 'flex-end', width: '100%' }}>
                {!isUser && (
                    <div style={{
                        width: '32px', height: '32px', borderRadius: '50%', background: `url(${aiAgent.headerAvatar}) center/cover no-repeat, #f1f5f9`,
                        flexShrink: 0, border: '1px solid rgba(0,0,0,0.05)',
                    }} />
                )}
                <div style={{
                    maxWidth: '80%', padding: '10px 14px', borderRadius: isUser ? '18px 18px 4px 18px' : '18px 18px 18px 4px',
                    background: isUser ? aiAgent.actionGradient : 'rgba(30, 41, 59, 0.8)', color: '#fff', fontSize: '13.5px', lineHeight: '1.6',
                    backdropFilter: 'blur(10px)', border: isUser ? 'none' : '1px solid rgba(255,255,255,0.08)', wordBreak: 'break-word',
                }}>
                    {formatMarkdown(cleanContent)}
                    {hasTrigger && <WhatsAppButton messages={allMessages} />}
                </div>
            </div>
            
            {/* Horizontal Product Recommendations */}
            {!isUser && msg.products && msg.products.length > 0 && (
                <ProductList products={msg.products} />
            )}

        </div>
    );
};


export default function AiChatbot() {
    const [isOpen, setIsOpen] = useState(false);
    const [sessionId, setSessionId] = useState(() => localStorage.getItem('vion_session_id'));
    const [userData, setUserData] = useState(null);
    const [messages, setMessages] = useState([]);
    const [input, setInput] = useState('');
    const [isLoading, setIsLoading] = useState(false);
    const messagesEndRef = useRef(null);
    const lastMessageRef = useRef(null);
    const inputRef = useRef(null);

    // Persistence: Load history if session exists
    useEffect(() => {
        const fetchHistory = async () => {
            if (!sessionId) return;
            try {
                const { data } = await axios.get(`/api/ai/get-history?session_id=${sessionId}`);
                if (data.messages && data.messages.length > 0) {
                    setMessages(data.messages);
                } else {
                    localStorage.removeItem('vion_session_id');
                    setSessionId(null);
                }
            } catch (err) {
                console.error('Failed to fetch chat history');
            }
        };
        fetchHistory();
    }, [sessionId]);

    useEffect(() => {
        if (isOpen) { 
            document.body.style.overflow = 'hidden';
            if (window.lenis) window.lenis.stop(); 
        } else { 
            document.body.style.overflow = 'unset';
            if (window.lenis) window.lenis.start(); 
        }
        return () => { 
            document.body.style.overflow = 'unset'; 
            if (window.lenis) window.lenis.start();
        };
    }, [isOpen]);

    useEffect(() => { 
        if (messages.length > 0 && !isLoading) {
            lastMessageRef.current?.scrollIntoView({ behavior: 'smooth', block: 'start' }); 
        } else {
            messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
        }
    }, [messages, isLoading]);

    const startChat = (id, data) => {
        setSessionId(id);
        localStorage.setItem('vion_session_id', id);
        setUserData(data);
    };

    const sendMessage = async () => {
        if (!input.trim() || isLoading || !sessionId) return;
        const userMessage = input.trim();
        setInput('');
        setMessages(prev => [...prev, { role: 'user', content: userMessage }]);
        setIsLoading(true);
        try {
            const { data } = await axios.post('/api/ai/chat', { message: userMessage, session_id: sessionId });
            setMessages(prev => [...prev, { role: 'assistant', content: data.response, products: data.products }]);
        } catch (err) {
            setMessages(prev => [...prev, { role: 'assistant', content: 'Maaf, saya sedang mengalami gangguan teknis. 🙏' }]);
        } finally { setIsLoading(false); }
    };

    return (
        <>
            <style>{`
                @keyframes typing { 0%, 60%, 100% { transform: translateY(0); opacity: 0.4; } 30% { transform: translateY(-6px); opacity: 1; } }
                @keyframes slideUp { from { opacity: 0; transform: translateY(20px) scale(0.95); } to { opacity: 1; transform: translateY(0) scale(1); } }
                @keyframes pulse { 0%, 100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4); } 50% { box-shadow: 0 0 0 12px rgba(16, 185, 129, 0); } }
                
                #ai-messages::-webkit-scrollbar { width: 6px; }
                #ai-messages::-webkit-scrollbar-track { background: transparent; }
                #ai-messages::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 10px; }
                #ai-messages::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.3); }
                .product-list-container::-webkit-scrollbar { display: none; }
                .chat-container { overscroll-behavior: contain; }
            `}</style>

            {isOpen && (
                <div 
                    id="ai-chatbot-window"
                    data-lenis-prevent
                    onWheel={e => e.stopPropagation()}
                    onTouchMove={e => e.stopPropagation()}
                    style={{
                        position: 'fixed', bottom: '130px', left: '20px', zIndex: 9999, width: 'calc(100% - 40px)', maxWidth: '400px', height: 'min(580px, 70vh)',
                        background: 'linear-gradient(160deg, #0f172a 0%, #1e293b 100%)', borderRadius: '24px', border: '1px solid rgba(255,255,255,0.1)',
                        boxShadow: '0 25px 60px rgba(0,0,0,0.5)', display: 'flex', flexDirection: 'column', overflow: 'hidden',
                        animation: 'slideUp 0.3s ease', backdropFilter: 'blur(20px)', overscrollBehavior: 'contain',
                    }}
                >

                    <div style={{ padding: '16px 20px', background: aiAgent.gradient, display: 'flex', alignItems: 'center', gap: '12px', flexShrink: 0, borderBottom: '1px solid rgba(0,0,0,0.05)' }}>
                        <div style={{ width: '44px', height: '44px', borderRadius: '50%', background: `url(${aiAgent.headerAvatar}) center/cover no-repeat, rgba(0,0,0,0.05)`, border: '1px solid rgba(0,0,0,0.1)' }} />
                        <div style={{ flex: 1 }}>
                            <div style={{ fontWeight: '700', color: '#1e293b', fontSize: '15px' }}>{aiAgent.name}</div>
                            <div style={{ color: '#64748b', fontSize: '12px' }}>{aiAgent.role} · Vion</div>
                        </div>
                        <button onClick={() => setIsOpen(false)} style={{ background: '#fee2e2', border: 'none', color: '#ef4444', width: '32px', height: '32px', borderRadius: '50%', cursor: 'pointer', display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: '20px', fontWeight: 'bold', transition: 'all 0.2s' }} onMouseEnter={e => e.currentTarget.style.background = '#fecaca'} onMouseLeave={e => e.currentTarget.style.background = '#fee2e2'}>×</button>
                    </div>

                    {!sessionId ? (
                        <div style={{ flex: 1, overflowY: 'auto', overscrollBehavior: 'contain' }}>
                            <LeadForm onComplete={startChat} />
                        </div>
                    ) : (
                        <>
                            <div 
                                id="ai-messages" 
                                className="chat-container" 
                                data-lenis-prevent
                                onWheel={e => e.stopPropagation()}
                                style={{ 
                                    flex: 1, 
                                    overflowY: 'auto', 
                                    padding: '16px', 
                                    display: 'flex', 
                                    flexDirection: 'column', 
                                    overscrollBehavior: 'contain', 
                                    height: '100%',
                                    position: 'relative',
                                    zIndex: 1
                                }}
                            >
                                {messages.map((msg, i) => (
                                    <div key={i} ref={i === messages.length - 1 ? lastMessageRef : null}>
                                        <MessageBubble msg={msg} allMessages={messages} />
                                    </div>
                                ))}
                                {isLoading && (
                                    <div style={{ display: 'flex', alignItems: 'flex-end', gap: '8px', marginBottom: '12px' }}>
                                        <div style={{ width: '32px', height: '32px', borderRadius: '50%', background: `url(${aiAgent.headerAvatar}) center/cover no-repeat, #f1f5f9`, border: '1px solid rgba(0,0,0,0.05)' }} />
                                        <div style={{ background: 'rgba(30, 41, 59, 0.8)', borderRadius: '18px 18px 18px 4px', border: '1px solid rgba(255,255,255,0.08)', padding: '12px 16px' }}>
                                            <div style={{ display: 'flex', gap: '4px' }}>
                                                {[0, 1, 2].map(i => <div key={i} style={{ width: '8px', height: '8px', borderRadius: '50%', background: '#94a3b8', animation: 'typing 1.4s infinite', animationDelay: `${i * 0.2}s` }} />)}
                                            </div>
                                        </div>
                                    </div>
                                )}
                                <div ref={messagesEndRef} />
                            </div>


                            <div style={{ padding: '16px', background: 'rgba(15, 23, 42, 0.5)', borderTop: '1px solid rgba(255,255,255,0.05)', flexShrink: 0 }}>
                                <div style={{ position: 'relative', display: 'flex', gap: '8px', alignItems: 'flex-end' }}>
                                    <textarea
                                        ref={inputRef}
                                        value={input}
                                        onChange={(e) => setInput(e.target.value)}
                                        onKeyDown={(e) => { if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); } }}
                                        placeholder="Tanya VION..."
                                        style={{
                                            flex: 1, background: 'rgba(30, 41, 59, 0.5)', border: '1px solid rgba(255,255,255,0.1)', borderRadius: '16px',
                                            padding: '12px 45px 12px 16px', color: '#fff', fontSize: '15px', outline: 'none', resize: 'none', height: '45px', maxHeight: '120px'
                                        }}
                                    />
                                    <button
                                        onClick={sendMessage}
                                        disabled={isLoading || !input.trim()}
                                        style={{
                                            position: 'absolute', right: '6px', bottom: '6px', width: '34px', height: '34px', borderRadius: '12px',
                                            background: input.trim() ? aiAgent.actionGradient : 'rgba(0,0,0,0.05)', border: 'none', color: '#fff',
                                            display: 'flex', alignItems: 'center', justifyContent: 'center', cursor: 'pointer', transition: 'all 0.2s'
                                        }}
                                    >
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round">
                                            <line x1="22" y1="2" x2="11" y2="13"></line>
                                            <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </>
                    )}
                </div>
            )}

            <button
                onClick={() => setIsOpen(!isOpen)}
                style={{
                    position: 'fixed', bottom: '24px', left: '24px', width: '130px', height: '130px',
                    background: 'transparent', border: 'none', cursor: 'pointer', zIndex: 9999,
                    display: 'flex', alignItems: 'center', justifyContent: 'center',
                    transition: 'all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275)',
                    transform: isOpen ? 'scale(0.8) rotate(5deg)' : 'scale(1)',
                }}
            >
                <div style={{ width: '100%', height: '100%', background: `url(${aiAgent.avatar}) center/contain no-repeat`, filter: 'drop-shadow(0 8px 16px rgba(0,0,0,0.2))' }} />
            </button>
        </>
    );
}
