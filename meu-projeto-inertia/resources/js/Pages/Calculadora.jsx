import React from "react";
import { useForm } from "@inertiajs/react";

export default function Calculadora({ total, sugestaoIa, dadosIA }) {
    const { data, setData, post, processing, errors } = useForm({
        valor_hora: "",
        horas: "",
        descricao: "",
        ajuste: "",
    });

    const enviarParaCalculo = (e) => {
        e.preventDefault();
        // O get() 'descricao' automaticamente junto com os outros dados
        post("/calculadora", {
            preserveState: true,
            preserveScroll: true,
        });
    };

    // Compatibilidade: dependendo do backend, `dadosIA` pode vir como:
    // 1) array de tarefas
    // 2) { tarefas: [] }
    // 3) { tarefas: { ... } } (objeto)
    const tarefas = Array.isArray(dadosIA)
        ? dadosIA
        : Array.isArray(dadosIA?.tarefas)
          ? dadosIA.tarefas
          : dadosIA?.tarefas && typeof dadosIA.tarefas === "object"
            ? Object.values(dadosIA.tarefas)
            : [];

    const totalSugerido = dadosIA?.total_sugerido ?? sugestaoIa ?? null;
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
                    {/* {sugestaoIa !== null && sugestaoIa !== undefined && (
                        <div className="mt-2 p-3 bg-indigo-50 rounded-lg border border-indigo-100 flex items-center gap-2">
                            <span className="text-xl">💡</span>
                            <p className="text-sm text-indigo-900">
                                Sugestão da IA para este escopo:
                                <span className="font-bold">
                                    {" "}
                                    {sugestaoIa} horas
                                </span>
                                .
                                <span className="text-gray-500 text-xs ml-1"></span>
                            </p>
                        </div>
                    )} */}
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
                {dadosIA && (
                    <div className="md:col-span-2 mt-4 p-4 bg-amber-50 rounded-xl border border-amber-200">
                        <label className="block text-xs font-bold text-amber-800 uppercase mb-2">
                            Não gostou da estimativa? Peça um ajuste:
                        </label>
                        <div className="flex gap-2">
                            <input
                                type="text"
                                value={data.ajuste}
                                onChange={(e) =>
                                    setData("ajuste", e.target.value)
                                }
                                placeholder="Ex: 'Considere que já tenho o layout pronto'..."
                                className="flex-1 p-2 text-sm border-amber-300 rounded-lg focus:ring-amber-500"
                            />
                            <button
                                onClick={enviarParaCalculo} // Reutiliza a função de post
                                className="bg-amber-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-amber-700"
                            >
                                Refinar
                            </button>
                        </div>
                    </div>
                )}

                {/* Coloque isso logo abaixo do card de sugestão principal */}
                {tarefas.length > 0 || totalSugerido !== null ? (
                    <div className="mt-6 border-t pt-6 w-full md:col-span-2">
                        <h3 className="text-sm font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <span className="bg-indigo-100 text-indigo-600 p-1 rounded">
                                📋
                            </span>
                            Decomposição de Tarefas Sugerida:
                        </h3>

                        <div className="space-y-3 w-full">
                            {tarefas.map((tarefa, index) => (
                                <div
                                    key={index}
                                    className="flex items-start gap-4 p-3 bg-white border border-gray-100 rounded-xl hover:shadow-md transition shadow-sm w-full"
                                >
                                    <div className="bg-gray-50 px-3 py-1 rounded-lg text-xs font-bold text-indigo-600 border">
                                        {tarefa.horas}h
                                    </div>
                                    <div>
                                        <p className="text-sm font-semibold text-gray-800">
                                            {tarefa.item ?? tarefa.nome}
                                        </p>
                                        <p className="text-xs text-gray-500">
                                            {tarefa.descricao}
                                        </p>
                                    </div>
                                </div>
                            ))}
                        </div>

                        <div className="mt-4 p-3 bg-gray-900 rounded-lg flex justify-between items-center text-white">
                            <span className="text-xs uppercase tracking-widest font-bold opacity-70">
                                Total Estimado
                            </span>
                            <span className="text-xl font-black">
                                {totalSugerido ?? 0} horas
                            </span>
                        </div>
                    </div>
                ) : null}
                {/* EXIBIÇÃO DA SUGESTÃO DA IA - FIM */}
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
