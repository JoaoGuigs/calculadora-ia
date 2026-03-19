import React from "react";
usePage;
import { useForm, usePage } from "@inertiajs/react";

export default function Calculadora({ total, sugestaoIa }) {
    const { data, setData, post, processing, errors } = useForm({
        valor_hora: "",
        horas: "",
        descricao: "", 
    });

    const enviarParaCalculo = (e) => {
        e.preventDefault();
        // O get() 'descricao' automaticamente junto com os outros dados
        post("/calculadora", {
            preserveState: true,
            preserveScroll: true,
        });
    };

    return (
        <div className="p-10 max-w-2xl mx-auto bg-white shadow-2xl rounded-2xl border mt-10 font-sans">
            <h1 className="text-3xl font-black mb-1 text-gray-900">
                Orçamento <span className="text-indigo-600">Inteligente</span>
            </h1>
            <p className="text-gray-500 mb-8">
                Descreva seu projeto e deixe nossa IA ajudar na estimativa.
            </p>

            <form
                onSubmit={enviarParaCalculo}
                className="grid grid-cols-1 md:grid-cols-2 gap-6"
            >
                <div className="md:col-span-2">
                    <label className="block text-sm font-semibold text-gray-700 mb-1">
                        Descrição do Projeto (Escopo)
                    </label>
                    <textarea
                        name="descricao"
                        value={data.descricao}
                        onChange={(e) => setData("descricao", e.target.value)}
                        placeholder="Ex: Criar uma landing page em React com formulário de contato..."
                        rows={4}
                        className={`w-full p-3 border rounded-xl focus:ring-2 focus:ring-indigo-200 transition ${errors.descricao ? "border-red-500" : "border-gray-300"}`}
                    />
                    {errors.descricao && (
                        <span className="text-red-500 text-xs mt-1">
                            {errors.descricao}
                        </span>
                    )}

                    {/* EXIBIÇÃO DA SUGESTÃO DA IA */}
                    {sugestaoIa && (
                        <div className="mt-2 p-3 bg-indigo-50 rounded-lg border border-indigo-100 flex items-center gap-2">
                            <span className="text-xl">💡</span>
                            <p className="text-sm text-indigo-900">
                                Sugestão da IA para este escopo:
                                <span className="font-bold">
                                    {" "}
                                    {sugestaoIa} horas
                                </span>
                                .
                                <span className="text-gray-500 text-xs ml-1">
                                </span>
                            </p>
                        </div>
                    )}
                </div>

                <div>
                    <label className="block text-sm font-medium text-gray-600 mb-1">
                        Sua Hora (R$)
                    </label>
                    <input
                        type="number"
                        value={data.valor_hora}
                        onChange={(e) => setData("valor_hora", e.target.value)}
                        className="w-full p-3 border border-gray-300 rounded-lg"
                    />
                </div>

                <div>
                    <label className="block text-sm font-medium text-gray-600 mb-1">
                        Horas Estimadas
                    </label>
                    <input
                        type="text" 
                        value={data.horas}
                        onChange={(e) => setData("horas", e.target.value)}
                        placeholder={
                            sugestaoIa && sugestaoIa !== "Erro na IA"
                                ? `Ex: ${sugestaoIa}`
                                : "Ex: 20"
                        }
                        className="w-full p-3 border border-gray-300 rounded-lg"
                    />
                </div>

                <div className="md:col-span-2">
                    <button
                        type="submit"
                        disabled={processing}
                        className="w-full bg-indigo-600 text-white py-4 rounded-xl font-bold hover:bg-indigo-700 disabled:bg-gray-400 transition flex justify-center items-center gap-2"
                    >
                        {processing ? (
                            <>
                                {" "}
                                <span className="animate-spin">⏳</span>{" "}
                                Analisando e Calculando...
                            </>
                        ) : (
                            "Analisar Escopo e Calcular"
                        )}
                    </button>
                </div>
            </form>

            <div className="mt-8 pt-6 border-t border-gray-100 flex justify-between items-end">
                <div>
                    <p className="text-gray-500 text-sm">
                        Valor Líquido Estimado:
                    </p>
                    <h2 className="text-5xl font-black text-indigo-900">
                        R$ {Number(total).toFixed(2)}
                    </h2>
                </div>
                <div className="text-xs text-gray-400 italic">
                    * Categorias disponíveis:
                </div>
            </div>
        </div>
    );
}
