using System;
namespace Quantum.Tala.Service
{
    public interface IDeathmatchService
    {
        System.Collections.Generic.List<string> SubtractVCoinBeforeStartSoi(System.Collections.Generic.List<Quantum.Tala.Service.Business.TalaUser> arrBankCredentialToSubtract, Quantum.Tala.Service.DTO.tournamentDTO tour);
    }
}
