using System;
namespace Quantum.Tala.Service
{
    interface IMoneyService
    {
        Quantum.Tala.Service.DTO.transactionDTO CreateTransation(Quantum.Tala.Service.DTO.transactionDTO p_dto);
        int SaveTransation(Quantum.Tala.Service.DTO.transactionDTO p_dto);
    }
}
