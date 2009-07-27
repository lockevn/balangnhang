using System;
namespace Quantum.Tala.Service
{
    public interface IPlayingService
    {
        void AdjustGold(string p_sUsername, int p_nValue, Quantum.Tala.Service.Business.EnumPlayingResult p_enumWhy, int p_nTourID);
    }
}
